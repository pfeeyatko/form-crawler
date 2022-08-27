<?php

require './vendor/autoload.php';

use Goutte\Client;

$formFields = [];
$client = new Client();

if (isset($_POST['submit'])) {
    $url = empty($_POST['url']) ? $_SERVER['HTTP_REFERER'] : $_POST['url'];

    try {
        $crawler = $client->request('GET', $url);
    } catch (Exception $e) {
        $error = $e;
    }

    if (isset($crawler)) {
        $crawler->filter('input')->each(function($node) {
            global $formFields;
            $req = is_null($node->attr('required')) ? '' : ' *Required';
    
            switch ($node->attr('type')) {
                case 'hidden':
                    break;
                case 'radio':
                case 'checkbox':
                    array_push($formFields, 'Input field: ' . $node->attr('name') . ', value: ' . $node->attr('value') . ', type: ' . $node->attr('type') . $req . PHP_EOL);
                    break;
                default:                    
                    array_push($formFields, 'Input field: ' . $node->attr('name') . ', type: ' . $node->attr('type') . $req . PHP_EOL);
                    break;
            }
        });

        $crawler->filter('select')->each(function($node) {
            global $formFields;
            $req = is_null($node->attr('required')) ? '' : ' *Required';
            array_push($formFields, 'Select field: ' . $node->attr('name') .  $req . ', Options: ' . PHP_EOL);
    
            foreach ($node->children() as $option) {
                array_push($formFields, '    ' . $option->nodeValue . PHP_EOL);
            }
        });
    
        $crawler->filter('textarea')->each(function($node) {
            global $formFields;
            $req = is_null($node->attr('required')) ? '' : ' *Required';
            array_push($formFields, 'Form field: ' . $node->attr('name') . ', type: textarea' . $req . PHP_EOL);
        });
    }
}

?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Form crawler</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>

  <body>

    <div class="container">
        <h1 class="page-header mt-5">Form crawler</h1>

        <p>Enter a URL below to a web page with a form on it. This script will then crawl the page and extract all of the form fields and related information.</p>

        <div class="well">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <div class="form-group">
                <label for="url">URL</label>
                <input type="text" class="form-control" id="url" name="url" placeholder="http://example.com" value="<?php if (isset($url)) { echo $url; } ?>" required>
              </div>
              <button type="submit" class="btn btn-primary" name="submit">Submit</button><br><br>
            </form>
        </div>

        <?php if (isset($_POST['submit'])): ?>
            <?php if (isset($error)): ?>

                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>

            <?php else: ?>

                <?php if (count($formFields) > 0): ?>

                    <div class="alert alert-success">
                        <pre style="margin:0;"><?php
                            foreach ($formFields as $field) {
                                echo $field;
                            }
                        ?></pre>
                    </div>

                <?php else: ?>

                    <div class="alert alert-warning">No form fields found</div>

                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

  </body>
</html>
