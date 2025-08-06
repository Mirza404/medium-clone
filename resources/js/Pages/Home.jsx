const Home = ({ message, user }) => {
  return (
    <div>
      <h1>{message}</h1>
      {user && <p>Hello, {user.name}</p>}
    </div>
  );
};

export default Home;
