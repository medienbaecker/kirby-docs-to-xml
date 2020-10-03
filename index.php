<?php

Kirby::plugin('medienbaecker/snippet-generator', [
  'routes' => [
    [
      'pattern' => 'docs/snippets.xml',
      'action'  => function () {
        if($reference = page("docs/reference")) {

          $docs = array();

          function addToArray($pages, &$json) {
            $array = array();
            foreach($pages as $page) {
              $parameters = array_column($page->parameters(), 'export');
              $body = array();
              $c = 0;

              $json[$page->title()->value()] = array(
                "string" => $page->methodName(),
                "description" => $page->excerpt()->escape()->value(),
                "parameters" => $parameters,
                "scope" => "php"
              );
              
            }
          }
          
          // Page methods
          addToArray(page("docs/reference/objects/page")->children()->listed(), $docs);
          
          $xml = '';
          foreach($docs as $method) {
            $xml .= '<completion string="' . $method['string'] . '"';
            if($method['parameters']) {
              $parameters = array();
              foreach($method['parameters'] as $parameter) {  
                if(strpos($parameter, '$') !== false) {
                  $parameter = strstr($parameter, '$');
                }
                $parameter = str_replace(']', '\]', $parameter);
                $parameters[] = '$[' . $parameter . ']';
              }
              $xml .= ">\n";
              $xml .= "  <behavior>\n";
              $xml .= "    <append>(";
              $xml .= implode(",", $parameters);
              $xml .= ")</append>\n";
              $xml .= "  </behavior>\n";
              $xml .= '</completion>';
            }
            else {
              $xml .= "/>";
            }
            $xml .= "\n";
          }
          
          return '<pre><code>' . htmlentities($xml) . '</code></pre>';
          
        }
      }
    ]
  ]
]);