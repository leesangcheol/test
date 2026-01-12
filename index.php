<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git 협업 테스트 프로젝트</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
        }

        h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        .badge {
            display: inline-block;
            padding: 10px 20px;
            background: #764ba2;
            color: white;
            border-radius: 25px;
            margin: 10px 0;
            font-weight: bold;
        }

        .info {
            margin: 30px 0;
            padding: 20px;
            background: #f7f7f7;
            border-radius: 10px;
            line-height: 1.8;
        }

        .info-item {
            margin: 10px 0;
            font-size: 1.1em;
        }

        .label {
            font-weight: bold;
            color: #667eea;
        }

        .emoji {
            font-size: 1.5em;
            margin-right: 10px;
        }

        footer {
            margin-top: 30px;
            color: #888;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Git 협업 & 자동 배포</h1>
        <div class="badge">배포 성공!</div>

        <div class="info">
            <div class="info-item">
                <span class="emoji">🌍</span>
                <span class="label">환경:</span>
                <?php
                    $env = getenv('APP_ENV') ?: '개발';
                    echo $env === 'production' ? '운영 서버' : '개발 서버';
                ?>
            </div>

            <div class="info-item">
                <span class="emoji">📅</span>
                <span class="label">배포 시간:</span>
                <?php echo date('Y-m-d H:i:s'); ?>
            </div>

            <div class="info-item">
                <span class="emoji">🔀</span>
                <span class="label">브랜치:</span>
                <?php echo getenv('GIT_BRANCH') ?: 'develop'; ?>
            </div>

            <div class="info-item">
                <span class="emoji">🐳</span>
                <span class="label">Docker 버전:</span>
                <?php echo getenv('APP_VERSION') ?: 'v1.0.0'; ?>
            </div>

            <div class="info-item">
                <span class="emoji">💻</span>
                <span class="label">PHP 버전:</span>
                <?php echo phpversion(); ?>
            </div>
        </div>

        <footer>
            Made with ❤️ using Git, GitHub Actions & Docker
        </footer>
    </div>
</body>
</html>
