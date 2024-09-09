<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #1a1a2e;
    }
    .game-menu {
        background-color: #16213e;
        color: #e94560;
        font-family: 'Arial', sans-serif;
        text-align: center;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 0 30px rgba(233, 69, 96, 0.3);
        width: 80%;
        max-width: 600px;
    }
    .game-title {
        font-size: 60px;
        margin-bottom: 40px;
        text-shadow: 3px 3px #0f3460;
        letter-spacing: 2px;
    }
    .menu-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .menu-button {
        background-color: #0f3460;
        color: #ffffff;
        border: none;
        padding: 20px 40px;
        margin: 15px;
        font-size: 28px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 80%;
        max-width: 300px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .menu-button:hover {
        background-color: #e94560;
        color: #ffffff;
        transform: scale(1.05);
        box-shadow: 0 0 15px rgba(233, 69, 96, 0.5);
    }
    .play-button {
        background-color: #4caf50;
    }
    .settings-button {
        background-color: #ffa500;
    }
    .exit-button {
        background-color: #f44336;
    }
    .cat-icon {
        font-size: 48px;
        margin: 0 15px;
        vertical-align: middle;
    }
</style>

<div class="game-menu">
    <h1 class="game-title">
        <span class="cat-icon">🐱</span>
        CatAttack
        <span class="cat-icon">😼</span>
    </h1>
    <div class="menu-buttons">
        <a href="{{ url('/game/level') }}" class="menu-button play-button">Jouer</a>
        <button class="menu-button settings-button">Paramètres</button>
        <button class="menu-button exit-button">Quitter</button>
    </div>
</div>