<?php
$router->before(".*", function($req) use($response){
    $response->setHeader("x-code","x-value");
});
$router->get("/home/{id}", function($req,$res,$arr){
    print_r($arr);
});
$router->get("/home", function($req, $res){
    $res->setBody("Home Page"); 
    $res->send();
});
$router->post("/home", function($req, $res){
    print_r($_POST);  
});
$router->dispatch($request, $response);



$dsn = 'mysql:host=localhost;dbname=cmcintosh;charset=utf8';
$username = 'http_user';
$password = 'bf6912';
$options = [ 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];


    try {
        $pdo = new PdoConnect($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Error connecting to database: " . $e->getMessage());
    }
    if ($pdo->isConnected()) {
        echo "Database connection established successfully.";
    } else {
        echo "Failed to connect to the database.";
    }
//$myobj = new MyClass();


     // Load the template file
        $dom = new DomDocument();
        $dom->loadHTML($this->fileLoader->load($this->template));

        // Set the title in the <title> tag
        $xpath = new DOMXPath($dom);
        $titleElements = $xpath->query('//title');
        if ($titleElements->length > 0) {
            $titleElements->item(0)->nodeValue = htmlspecialchars($title);
        }

        // Save the modified HTML back to the template
        $this->fileLoader->save($this->template, $dom->saveHTML());

?>
<!-- REACT-DOM BABEL JQUERY -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HttpStack.tech - A portfolio showcasing web development projects and skills.">
    <meta name="keywords" content="portfolio, web development, projects, skills, HttpStack">
    <meta name="author" content="HttpStack Team">
    <link rel="icon" href="public/assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="public/assets/images/apple-touch-icon.png">
    <title>httpstack.tech - Portfolio</title>
    <link rel="stylesheet" href="public/assets/css/styles.css">
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/babel-standalone@7.16.7/babel.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/babel" src="public/assets/js/app.js"></script>
</head>



<?php
            $strType = $objAsset['type'] ?? ''; // Access type using array key
            $strFileExt = pathinfo($objAsset['filename'], PATHINFO_EXTENSION);
            echo $objAsset['filename'];
            $src = $this->fileLoader->findFile($objAsset['filename'], null, $strFileExt);
            $src = str_replace(DOC_ROOT, "", $src);
            switch ($strType) {
                case "sheet":
                    if ($objHead) {
                        $objLink = $dom->createElement("link");
                        $objLink->setAttribute("rel", "stylesheet");
                        $objLink->setAttribute("href", $src);
                        if (isset($objAsset['media'])) { // Access media using array key
                            $objLink->setAttribute("media", $objAsset['media']);
                        }
                        $objHead->appendChild($objLink);
                    }
                    break;

                case "script":
                    if ($objBody) { // Scripts can go in head or body, placing in head for simplicity unless specified
                        $objScript = $dom->createElement("script");
                        $objScript->setAttribute("src", $src);
                        if (isset($objAsset['async']) && $objAsset['async']) { // Access async using array key
                            $objScript->setAttribute("async", "true");
                        }
                        if (isset($objAsset['defer']) && $objAsset['defer']) { // Access defer using array key
                            $objScript->setAttribute("defer", "true");
                        }
                        $objBody->appendChild($objScript);
                    }
                    break;

                case "font":
                    if ($objHead && !empty($src)) {
                        // Check if it's a Google Fonts URL
                        if (str_contains($src, 'fonts.googleapis.com') || str_contains($src, 'fonts.gstatic.com')) {
                            $objLink = $dom->createElement("link");
                            $objLink->setAttribute("rel", "stylesheet");
                            $objLink->setAttribute("href", $src);
                            $objHead->appendChild($objLink);
                        } else {
                            // Assume it's a local font file, use preload
                            $extension = pathinfo($src, PATHINFO_EXTENSION);
                            if (in_array($extension, ['woff', 'woff2', 'ttf', 'otf'])) {
                                $objLink = $dom->createElement("link");
                                $objLink->setAttribute("rel", "preload");
                                $objLink->setAttribute("href", $src);
                                $objLink->setAttribute("as", "font");
                                $objLink->setAttribute("type", "font/{$extension}"); // e.g., font/woff2
                                $objLink->setAttribute("crossorigin", "anonymous"); // Required for font preloading
                                $objHead->appendChild($objLink);
                            } else {
                                error_log("Unsupported font extension for preload: " . $src);
                            }
                        }
                    }
                    break;

                case "image":
                    if (!empty($src)) {
                        $imagePreloadUrls[] = $src; // Collect image URLs
                    }
                    break;

                default:
                    error_log("Unknown asset type encountered: " . $strType);
                    break;
            }


$pdo = new PDO("mysql:host")
?>