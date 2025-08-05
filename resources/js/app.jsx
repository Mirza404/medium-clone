import "./bootstrap";
import Alpine from "alpinejs";
import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";

window.Alpine = Alpine;
Alpine.start();

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });
        return pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});

