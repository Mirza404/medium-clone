import { createServer } from "node:http";
import { readFileSync, existsSync } from "node:fs";
import { fileURLToPath } from "node:url";
import { dirname, join } from "node:path";
import { initChatModel } from "langchain";

hydrateEnv();

const PORT = Number(process.env.PORT ?? 4000);
const apiKey = process.env.GOOGLE_API_KEY;
const internalKey = process.env.INTERNAL_SHARED_SECRET;

if (!apiKey) {
    console.error("Missing GOOGLE_API_KEY. Set it in services/langchain/.env before starting the server.");
    process.exit(1);
}

const model = await initChatModel("google-genai:gemini-2.5-flash-lite", {
    apiKey,
});

const server = createServer(async (req, res) => {
    if (!req.url) {
        res.writeHead(400, { "Content-Type": "application/json" });
        res.end(JSON.stringify({ error: "Missing URL in request." }));
        return;
    }

    const url = new URL(req.url, `http://localhost:${PORT}`);

    if (req.method === "GET" && url.pathname === "/health") {
        res.writeHead(200, { "Content-Type": "application/json" });
        res.end(JSON.stringify({ status: "ok" }));
        return;
    }

    if (req.method === "POST" && url.pathname === "/chat") {
        if (!authorize(req)) {
            res.writeHead(401, { "Content-Type": "application/json" });
            res.end(JSON.stringify({ error: "Unauthorized" }));
            return;
        }

        let bodyText = "";
        for await (const chunk of req) {
            bodyText += chunk.toString();
        }

        let prompt;
        try {
            const parsed = bodyText ? JSON.parse(bodyText) : {};
            prompt = parsed.prompt;
        } catch {
            res.writeHead(400, { "Content-Type": "application/json" });
            res.end(JSON.stringify({ error: "Body must be valid JSON." }));
            return;
        }

        if (typeof prompt !== "string" || prompt.trim() === "") {
            res.writeHead(400, { "Content-Type": "application/json" });
            res.end(JSON.stringify({ error: "Provide a non-empty 'prompt' field." }));
            return;
        }

        try {
            const aiMessage = await model.invoke(prompt);
            const message = extractMessage(aiMessage);
            res.writeHead(200, { "Content-Type": "application/json" });
            res.end(
                JSON.stringify({
                    prompt,
                    response: message,
                    usage: aiMessage?.usageMetadata ?? null,
                }),
            );
        } catch (error) {
            console.error("LangChain invocation failed:", error);
            res.writeHead(500, { "Content-Type": "application/json" });
            res.end(JSON.stringify({ error: "Agent call failed. Check server logs for details." }));
        }
        return;
    }

    res.writeHead(404, { "Content-Type": "application/json" });
    res.end(JSON.stringify({ error: "Route not found." }));
});

server.listen(PORT, () => {
    console.log(`LangChain microservice running at http://localhost:${PORT}`);
    console.log(`POST http://localhost:${PORT}/chat with { "prompt": "..." } to test.`);
});

function hydrateEnv() {
    const currentDir = dirname(fileURLToPath(import.meta.url));
    const envPath = join(currentDir, ".env");
    if (!existsSync(envPath)) {
        return;
    }

    const lines = readFileSync(envPath, "utf8").split(/\r?\n/);
    for (const line of lines) {
        const trimmed = line.trim();
        if (!trimmed || trimmed.startsWith("#")) {
            continue;
        }
        const separatorIndex = trimmed.indexOf("=");
        if (separatorIndex === -1) {
            continue;
        }
        const key = trimmed.slice(0, separatorIndex).trim();
        const value = trimmed.slice(separatorIndex + 1).trim().replace(/^['"]|['"]$/g, "");

        if (!(key in process.env)) {
            process.env[key] = value;
        }
    }
}

function extractMessage(aiMessage) {
    const content = aiMessage?.content;

    if (typeof content === "string") {
        return content;
    }

    if (Array.isArray(content)) {
        return content
            .map((part) => {
                if (typeof part === "string") {
                    return part;
                }
                if (typeof part?.text === "string") {
                    return part.text;
                }
                if (typeof part?.data === "string") {
                    return part.data;
                }
                return "";
            })
            .join("")
            .trim();
    }

    return "";
}

function authorize(req) {
    if (!internalKey) {
        return true;
    }

    const header = req.headers?.["x-internal-key"];
    if (Array.isArray(header)) {
        return header.includes(internalKey);
    }

    return typeof header === "string" && header === internalKey;
}
