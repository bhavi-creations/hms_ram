<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        h1 {
            font-size: 2.5rem;
            color: #dc3545;
            margin-top: 0;
            font-weight: 600;
        }
        p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #555;
        }
        .code {
            background: #eee;
            padding: 10px 15px;
            border-radius: 8px;
            font-family: monospace;
            display: inline-block;
            margin-top: 20px;
            color: #444;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= esc($title) ?></h1>
        <p><?= esc($message) ?></p>
        <p>If you need assistance, please provide the error code to support.</p>
    </div>
</body>
</html>
