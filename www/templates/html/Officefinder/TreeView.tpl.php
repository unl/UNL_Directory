<?php
UNL_Officefinder::setReplacementData('pagetitle', '<h1>Departmental Hierarchy</h1>');
?>
<p>This view contains the departmental hierarchy found in SAP, the University's financial system.</p>
<style>
  #dcf-main ul.tree, #dcf-main ul.tree ul {
    list-style-type: none;
    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAKAQMAAABPHKYJAAAAA1BMVEWIiIhYZW6zAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH1ggGExMZBky19AAAAAtJREFUCNdjYMAEAAAUAAHlhrBKAAAAAElFTkSuQmCC) repeat-y;
    margin: 0;
    padding: 0;
  }
  #dcf-main ul.tree ul {
      margin-left: 10px;
  }
  #dcf-main ul.tree li {
    margin: 0;
    padding: 0 12px;
    line-height: 20px;
    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAUAQMAAACK1e4oAAAABlBMVEUAAwCIiIgd2JB2AAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9YIBhQIJYVaFGwAAAARSURBVAjXY2hgQIf%2FGTDFGgDSkwqATqpCHAAAAABJRU5ErkJggg%3D%3D) no-repeat;
  }
  #dcf-main ul.tree li:last-child {
    background: #fff url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAUAQMAAACK1e4oAAAABlBMVEUAAwCIiIgd2JB2AAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9YIBhQIIhs%2Bgc8AAAAQSURBVAjXY2hgQIf%2FGbAAAKCTBYBUjWvCAAAAAElFTkSuQmCC) no-repeat;
  }
  .academic {
      background-color:#C2FFE0;
  }
  .org_unit {
      font-size:75%;
      color:black;
  }
  .suppressed {
      text-decoration:line-through;
  }
</style>
<div class="dcf-grid">
    <div class="dcf-col-100% dcf-col-25%-start@md">
      <ul>
        <li><span class="academic">Green</span> listings are shown on the <a href="academic">Academic Departments list</a></li>
        <li><span class="suppressed">Struck</span> listings have no SAP appointments or child listings, and are hidden from the public</li>
      </ul>
    </div>
    <div class="dcf-col-100% dcf-col-75%-end@md">
        <ul class="tree">
            <?php
            $depth = 0;
            foreach ($context as $department) {
                
                if ($context->getDepth() > $depth) {
                    echo '<ul>';
                } elseif ($context->getDepth() < $depth) {
                    // Close the current list item
                    echo '</li>';
                    // Loop through until we reach the previous depth
                    echo str_repeat('</ul></li>', $depth - $context->getDepth());
                } elseif($context->getDepth()==$depth) {
                    echo '</li>';
                }
                echo PHP_EOL;
                echo str_repeat(' ', $context->getDepth());
                $li_class = 'd' . $context->getDepth();
                $d_class  = '';
            
                if ($department->suppress) {
                    $li_class .= ' suppressed';
                }
                if ($department->academic) {
                    $d_class .= ' academic';
                }
                echo '<li class="' . $li_class . '"><a href="'.$department->getURL().'" class="' . $d_class . '">'.(($department->name)?$department->name:'/--NULL--/').' <span class="org_unit">('.$department->org_unit . ')</span></a>';
                $depth = $context->getDepth();
            }
            ?>
            </li>
        </ul>
    </div>
</div>