<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatAttack</title>
    <style>
        body, html {
            background-color: #1a1a2e;
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        #gameCanvas {
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <canvas id="gameCanvas"></canvas>

    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        const cat = {
            x: 50,
            y: 200,
            width: 40,
            height: 40,
            speed: 5,
            jumpForce: 15,
            gravity: 0.3,
            isJumping: false,
            velocityY: 0,
            velocityX: 0
        };

        const levelWidth = 10000;
        const groundHeight = 50;
        let camera = { x: 0, y: 0 };

        let terrain = [];
        let enemies = [];
        const obstacleTypes = ['platform', 'hole', 'platform_with_spike', 'moving_platform', 'high_platform'];

        function generateTerrain() {
            let x = 0;
            let lastObstacleWasHole = false;

            const initialPlatformWidth = 300;
            terrain.push({ 
                type: 'platform', 
                x: 0, 
                y: canvas.height - groundHeight, 
                width: initialPlatformWidth, 
                height: groundHeight,
                color: '#00FF00'
            });
            x += initialPlatformWidth;

            while (x < levelWidth - 300) {
                let type;
                do {
                    type = obstacleTypes[Math.floor(Math.random() * obstacleTypes.length)];
                } while (type === 'hole' && lastObstacleWasHole);

                const width = Math.random() * 200 + 100;
                
                switch(type) {
                    case 'platform':
                        terrain.push({ type, x, y: canvas.height - groundHeight, width, height: groundHeight, color: '#0f3460' });
                        lastObstacleWasHole = false;
                        break;
                    case 'hole':
                        terrain.push({ type, x, width });
                        lastObstacleWasHole = true;
                        break;
                    case 'platform_with_spike':
                        terrain.push({ 
                            type: 'platform', 
                            x, 
                            y: canvas.height - groundHeight, 
                            width, 
                            height: groundHeight, 
                            color: '#0f3460',
                            hasSpike: true
                        });
                        lastObstacleWasHole = false;
                        break;
                    case 'moving_platform':
                        terrain.push({
                            type: 'moving_platform',
                            x,
                            y: canvas.height - groundHeight - 100,
                            width: 100,
                            height: 20,
                            color: '#FFA500',
                            speed: 2,
                            direction: 1,
                            minX: x,
                            maxX: x + 200
                        });
                        lastObstacleWasHole = false;
                        break;
                    case 'high_platform':
                        const highPlatformHeight = Math.random() * 200 + 100;
                        terrain.push({
                            type: 'platform',
                            x,
                            y: canvas.height - groundHeight - highPlatformHeight,
                            width,
                            height: 20,
                            color: '#4B0082'
                        });
                        lastObstacleWasHole = false;
                        break;
                }
                
                // Add enemy with 20% chance
                if (Math.random() < 0.2) {
                    enemies.push({
                        x: x + width / 2,
                        y: canvas.height - groundHeight - 30,
                        width: 30,
                        height: 30,
                        speed: Math.random() * 2 + 1,
                        direction: Math.random() < 0.5 ? -1 : 1,
                        minX: x,
                        maxX: x + width
                    });
                }
                
                x += width;
            }

            terrain.push({ 
                type: 'platform', 
                x: levelWidth - 300, 
                y: canvas.height - groundHeight, 
                width: 300, 
                height: groundHeight,
                color: '#FFFFFF'
            });
        }

        function resetGame() {
            cat.x = 50;
            cat.y = canvas.height - groundHeight - cat.height;
            cat.velocityY = 0;
            cat.velocityX = 0;
            cat.isJumping = false;
            camera.x = 0;
        }

        function gameLoop() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            camera.x = cat.x - canvas.width / 4;

            ctx.save();
            ctx.translate(-camera.x, 0);

            terrain.forEach(obstacle => {
                if (obstacle.type === 'platform' || obstacle.type === 'moving_platform') {
                    ctx.fillStyle = obstacle.color;
                    ctx.fillRect(obstacle.x, obstacle.y, obstacle.width, obstacle.height);
                    
                    if (obstacle.hasSpike) {
                        ctx.fillStyle = '#ff0000';
                        ctx.beginPath();
                        ctx.moveTo(obstacle.x + obstacle.width / 2 - 15, obstacle.y);
                        ctx.lineTo(obstacle.x + obstacle.width / 2, obstacle.y - 30);
                        ctx.lineTo(obstacle.x + obstacle.width / 2 + 15, obstacle.y);
                        ctx.closePath();
                        ctx.fill();
                    }

                    if (obstacle.type === 'moving_platform') {
                        obstacle.x += obstacle.speed * obstacle.direction;
                        if (obstacle.x <= obstacle.minX || obstacle.x + obstacle.width >= obstacle.maxX) {
                            obstacle.direction *= -1;
                        }
                    }
                }
            });

            enemies.forEach(enemy => {
                ctx.fillStyle = '#FF0000';
                ctx.fillRect(enemy.x, enemy.y, enemy.width, enemy.height);
                enemy.x += enemy.speed * enemy.direction;
                if (enemy.x <= enemy.minX || enemy.x + enemy.width >= enemy.maxX) {
                    enemy.direction *= -1;
                }
            });

            cat.velocityY += cat.gravity;
            cat.y += cat.velocityY;
            cat.x += cat.velocityX;

            let onGround = false;
            terrain.forEach(obstacle => {
                if (obstacle.type === 'platform' || obstacle.type === 'moving_platform') {
                    if (
                        cat.y + cat.height > obstacle.y &&
                        cat.y < obstacle.y + obstacle.height &&
                        cat.x + cat.width > obstacle.x &&
                        cat.x < obstacle.x + obstacle.width
                    ) {
                        cat.y = obstacle.y - cat.height;
                        cat.velocityY = 0;
                        cat.isJumping = false;
                        onGround = true;

                        if (obstacle.hasSpike && 
                            cat.x + cat.width > obstacle.x + obstacle.width / 2 - 15 &&
                            cat.x < obstacle.x + obstacle.width / 2 + 15) {
                            resetGame();
                        }

                        if (obstacle.type === 'moving_platform') {
                            cat.x += obstacle.speed * obstacle.direction;
                        }
                    }
                }
            });

            enemies.forEach(enemy => {
                if (
                    cat.x < enemy.x + enemy.width &&
                    cat.x + cat.width > enemy.x &&
                    cat.y < enemy.y + enemy.height &&
                    cat.y + cat.height > enemy.y
                ) {
                    resetGame();
                }
            });

            if (cat.y > canvas.height) {
                resetGame();
            }

            const catImage = new Image();
            catImage.src = 'https://s3-us-west-2.amazonaws.com/mb.images/vinafrog/listing/VFSIL0095.jpg';
            ctx.drawImage(catImage, cat.x, cat.y, cat.width, cat.height);

            ctx.restore();

            if (cat.x > levelWidth - 300 && cat.y === canvas.height - groundHeight - cat.height) {
                alert('Level completed!');
                resetGame();
            }

            requestAnimationFrame(gameLoop);
        }

        const keys = {};

        document.addEventListener('keydown', (event) => {
            keys[event.key] = true;
            handleMovement();
        });

        document.addEventListener('keyup', (event) => {
            keys[event.key] = false;
            handleMovement();
        });

        function handleMovement() {
            cat.velocityX = 0;

            if (keys['ArrowLeft']) {
                cat.velocityX = -cat.speed;
            }
            if (keys['ArrowRight']) {
                cat.velocityX = cat.speed;
            }
            if (keys['ArrowUp'] && !cat.isJumping) {
                cat.velocityY = -cat.jumpForce;
                cat.isJumping = true;
            }
        }

        generateTerrain();
        resetGame();
        gameLoop();
    </script>
</body>
</html>
