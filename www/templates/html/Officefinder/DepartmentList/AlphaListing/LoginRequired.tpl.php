<!doctype html>
<html lang="en">
<head>
    <title>University of Nebraska–Lincoln Directory</title>
    <style>
        html {
            font-size: 12px;
            color: #000;
        }
        body {
            column-count: 2;
            -webkit-column-count: 2;
            -moz-column-count: 2;
        }
        ol {
            list-style: none;
            padding-left: 1em;
        }
        a {
            text-decoration: none;
            color: #000;
        }
        abbr {
            text-decoration: none;
        }

        .phone.icon-print:before {
            content: 'Fax: ';
        }

        .on-campus-dialing + a {
            display: none;
        }
    </style>
</head>
<body>
<h3>University of Nebraska–Lincoln Directory<br>Prepared for <?php echo $context->user; ?> on <?php echo date('F jS, Y'); ?></h3>
<?php echo $savvy->render($context->output); ?>
