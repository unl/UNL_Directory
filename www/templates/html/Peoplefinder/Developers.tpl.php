<?php
$page->addHeadLink('https://cdn.jsdelivr.net/highlight.js/9.2.0/styles/solarized-dark.min.css', 'stylesheet');
?>

<div class="dcf-grid dcf-col-gap-vw">
    <div class="dcf-col-100% dcf-col-75%-start@md dcf-pb-6">
        <?php
        $resource = "UNL_Peoplefinder_Developers_" . $context->resource;
        $resource = new $resource;
        ?>
        <div class="resource">
            <h2 id="instance" class="sec_main">API: <?php echo $resource->getTitle(); ?> Resource</h2>

            <h3 id="instance-uri">Resource URI</h3>
            <p>
                <code>
                    <?php
                    $uri = $resource->getURI();
                    if (substr($uri, 0, 2) == '//') {
                        $uri = 'http:' . $uri;
                    }
                    echo $uri;
                    ?>
                </code>
            </p>

            <p><?php echo $resource->getDescription() ?></p>

            <h3 id="instance-get-example-1">Example</h3>
            <ul class="wdn_tabs">
                <?php
                foreach ($resource->getAvailableFormats() as $format) {
                    echo '<li><a href="#'.$format.'">'.$format.'</a></li>';
                }
                ?>
            </ul>
            <div class="wdn_tabs_content">
                <?php foreach ($resource->getAvailableFormats() as $format): ?>

                    <?php
                    $url = str_replace('{format}', $format, $resource->getExampleURI());
                    
                    $method_name = 'get' . ucfirst($format) . 'Properties';
                    ?>
                    <div id="<?php echo $format; ?>">
                        <pre><code>GET <?php echo $url; ?></code></pre>
                        <?php if (count($resource->$method_name())): ?>
                            <h2>Resource Properties</h2>
                            <table class="zentable neutral dcf-table dcf-txt-sm">
                            <thead><tr><th>Property</th><th>Description</th></tr></thead>
                            <tbody>
                                <?php foreach ($resource->$method_name() as $property => $description): ?>
                                    <tr>
                                      <td><?php echo $property ?></td>
                                      <td><?php echo $description ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                        <h2>Response</h2>
                        <?php
                        //Get the output.
                        if (!$result = file_get_contents($url)) {
                            $result = "Sorry, we could not get a sample for you.";
                        }

                        switch ($format) {
                            case "json":
                                $code = 'javascript';
                                //Pretty print it
                                $result = json_decode($result);
                                $result = json_encode($result, JSON_PRETTY_PRINT);
                                break;
                            case "xml":
                                $code = "xml";
                                break;
                            default:
                                $code = "html";
                        }
                        ?>
                        <pre class="code">
                            <code class="<?php echo $code; ?>"><?php echo htmlentities($result); ?></code>
                        </pre>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="dcf-col-100% dcf-col-25%-end@md">
        <nav id="resources" aria-label="API Navigation" class="zenbox primary">
            <h2>Directory API</h2>
            <p>The following is a list of resources for Directory.</p>
            <ul>
                <?php foreach ($context->resources as $resource => $name): ?>
                    <li><a href="<?php echo UNL_Peoplefinder::$url ?>developers/?resource=<?php echo $resource ?>"><?php echo $name ?></a></li>
                <?php endforeach ?>
            </ul>
        </nav>
        <div class="zenbox neutral">
            <h2>Format Information</h2>
            <p>The following is a list of formats used in Directory.</p>
            <ul>
                <li><a href="http://www.json.org/">JSON (JavaScript Object Notation)</a></li>
                <li><a href="http://en.wikipedia.org/wiki/XML">XML (Extensible Markup Language)</a></li>
                <li>Partial - The un-themed main content area of the page.</li>
            </ul>
        </div>
    </div>
</div>

<?php
  $page->addScriptDeclaration("require(['jquery', 'https://cdn.jsdelivr.net/highlight.js/9.2.0/highlight.min.js'], function ($, hljs) {
        $('.resource pre.code code').each(function () {
            hljs.highlightBlock(this);
        });
    })");
?>
