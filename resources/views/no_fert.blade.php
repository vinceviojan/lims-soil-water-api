<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Fertilizer Recommendation</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 100px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h2 {
            color: #444;
            margin-bottom: 10px;
        }

        p {
            color: #666;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn:hover {
            background: #45a049;
        }
    </style>

</head>
<body>

    <div class="container">
        <h2>No Fertilizer Recommendation Available</h2>
        <p>
            Sorry, we couldn’t generate a fertilizer recommendation based on the provided inputs.
            Please check your data and try again.
        </p>

        <a href="{{ url()->previous() }}" class="btn">Go Back</a>
    </div>

</body>
</html>
