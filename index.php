<?php

require './vendor/autoload.php';

use Goutte\Client;

if (isset($_POST['submit'])) {
    $url = empty($_POST['url']) ? $_SERVER['HTTP_REFERER'] : $_POST['url'];
    $client = new Client();

    try {
        $crawler = $client->request('GET', $url);
    } catch (Exception $e) {
        $error = $e;
    }
}

?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Form Crawler</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>

  <body>

    <div class="container">
        <h1 class="page-header mt-5">Form Crawler</h1>

        <p>Enter a URL below to a web page with a form on it. This script will then crawl the page and extract all of the form fields and related information.</p><br>

        <div class="well">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <div class="form-group">
                <label for="url">URL</label>
                <input type="text" class="form-control" id="url" name="url" placeholder="http://example.com" value="<?php if (isset($url)) { echo $url; } ?>" required>
              </div>
              <button type="submit" class="btn btn-primary" name="submit">Submit</button><br><br>
            </form>
        </div>

        <?php if (!isset($error)): ?>

            <?php if (isset($crawler)): ?>
                <pre><?php
                    $crawler->filter('input')->each(function($node) {

                        $req = is_null($node->attr('required')) ? '' : ' *Required';

                        switch ($node->attr('type')) {
                            case 'hidden':
                                break;
                            case 'radio':
                            case 'checkbox':
                                echo 'Input field: ' . $node->attr('name') . ', value: ' . $node->attr('value') . ', type: ' . $node->attr('type') . $req . PHP_EOL;
                                break;
                            default:
                                echo 'Input field: ' . $node->attr('name') . ', type: ' . $node->attr('type') . $req . PHP_EOL;
                                break;
                        }

                    });

                    $crawler->filter('select')->each(function($node) {
                        $req = is_null($node->attr('required')) ? '' : ' *Required';

                        echo 'Select field: ' . $node->attr('name') .  $req . ', Options: ' . PHP_EOL;

                        foreach ($node->children() as $option) {
                            echo '    ' . $option->nodeValue . PHP_EOL;
                        }
                    });

                    $crawler->filter('textarea')->each(function($node) {
                        $req = is_null($node->attr('required')) ? '' : ' *Required';

                        echo 'Form field: ' . $node->attr('name') . ', type: textarea' . $req . PHP_EOL;
                    });
                ?>
                </pre>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>

  </body>
</html>
