<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Form Submission Test</h3>
            </div>
            <div class="card-body">
                <p>This page will `var_dump` the received `$_POST` and `$_FILES` data on submission.</p>

                <?php
                // This block will only execute on a POST request
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo '<div class="alert alert-success mt-4"><h3>POST Data Received:</h3></div>';
                    echo '<pre class="bg-dark text-white p-3 rounded">';
                    var_dump($_POST);
                    echo '</pre>';

                    echo '<div class="alert alert-info mt-4"><h3>Files Data Received:</h3></div>';
                    echo '<pre class="bg-dark text-white p-3 rounded">';
                    var_dump($_FILES);
                    echo '</pre>';

                    echo '<p class="mt-4">If you see this, your form and PHP environment are working correctly.</p>';
                } else {
                    echo '<div class="alert alert-info mt-4">Please submit the form below.</div>';
                }
                ?>

                <hr>

                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="result_1">Result 1</label>
                        <textarea name="result_1" id="result_1" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="files_1">Upload Files (Reports, Images, etc.)</label>
                        <input type="file" name="files_1[]" id="files_1" class="form-control" multiple>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Test Form</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
