<?php
require 'vendor/autoload.php';
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\String\Slugger\AsciiSlugger;
use League\CommonMark\Environment\Environment as CmEnv;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\MarkdownConverter;
use Twig\Environment as TwigEnv;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\String\StringExtension;

$start = microtime(true);
$startMem = round(memory_get_usage()/1048576,2); 
echo "\nMemory Consumption is $startMem MB\n";


// Default Settings - overriden by settings.yaml if it exists
const SOURCE_ROOT = ".";
const TEMPLATES = __DIR__ . '/templates/';
const YAML_SETTINGS = SOURCE_ROOT . '/data/settings.yaml';
const YAML_METADATA = SOURCE_ROOT . '/data/metadata.yaml';
const YAML_MENUS = SOURCE_ROOT . '/data/menus.yaml';
const TEMP_FOLDER = __DIR__ . '/.CW_TEMP';

$staticDirs = ['assets'];
$staticFiles = ['robots.txt','feed/pretty-feed-v3.xsl'];
$sourceDirs = ['posts', 'pages', 'feed'];
$outputDir = '_site';
$theme = "default";
$themeExtends = "";
$templatesDir ="";
$templatesExtendsDir = "";


//YAML content - read
$settings = [];  
if (file_exists(YAML_SETTINGS)) {
    $settings = Yaml::parseFile(YAML_SETTINGS);
}
//check exists first - maybe metadata should always exist, else create its values?
$metadata = Yaml::parseFile(YAML_METADATA);
//check exists first
$yamlMenus = [];
if (file_exists(YAML_MENUS)) {
    $yamlMenus = Yaml::parseFile(YAML_MENUS);
}

//override defaults with settings.yaml values, if they exist
if(array_key_exists('staticDirs',$settings)) {
    $staticDirs = $settings['staticDirs'];
}
if(array_key_exists('staticFiles',$settings)) {
    $staticFiles = $settings['staticFiles'];
}
if(array_key_exists('sourceDirs',$settings)) {
    $sourceDirs = $settings['sourceDirs'];
}
if(array_key_exists('outputDir',$settings)) {
    $outputDir = $settings['outputDir'];
}
if(array_key_exists('theme',$settings)) {
    $theme = $settings['theme'];
}
if(array_key_exists('themeExtends',$settings)) {
    $themeExtends = $settings['themeExtends'];
    $templatesExtendsDir = TEMPLATES . $themeExtends . '/';
}
$templatesDir = TEMPLATES . $theme . '/';

// add leading and trailing slash if outputDir doesn't have it
if (substr($outputDir, 0, 1) != '/') {
    $outputDir = '/' . $outputDir;
}
if (substr($outputDir, -1) != '/') {
    $outputDir = $outputDir . '/';
}
$outputDir = '.' . $outputDir;

//echo "\n outputdir: " . $outputDir;

//create temporary folder __TEMP
//echo "\nExtends: " . $templatesExtendsDir;
//echo "\nTemplates: " . $templatesDir;
$fs = new Filesystem();
$fs->mkdir(TEMP_FOLDER);
//copy recursively all files from theme folder to TEMP folder
if(strlen($templatesExtendsDir) > 0) {
    $fs->mirror($templatesExtendsDir, TEMP_FOLDER);
}
//copy recursively all template extends files to TEMP folder overwriting any that exist
$fs->mirror($templatesDir, TEMP_FOLDER);

// SETTING UP MarkDown converter
// Define your configuration, if needed
// Configure the Environment with all the CommonMark parsers/renderers
// Add the extensions
//$config = [];

// Heading Permalink Extension configuration
$config = [
    'heading_permalink' => [
        'html_class' => 'heading-permalink',
        'id_prefix' => '',
        'apply_id_to_heading' => true,
        'heading_class' => '',
        'fragment_prefix' => '',
        'insert' => 'none',
        'min_heading_level' => 1,
        'max_heading_level' => 6,
        'title' => 'Permalink',
        'symbol' => HeadingPermalinkRenderer::DEFAULT_SYMBOL,
        'aria_hidden' => true,
    ],
];
$environment = new CmEnv($config);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new GithubFlavoredMarkdownExtension());
$environment->addExtension(new FrontMatterExtension());
$environment->addExtension(new HeadingPermalinkExtension());
$converter = new MarkdownConverter($environment);

// SETTING UP Twig for file loading
$loader = new FilesystemLoader(TEMP_FOLDER);
$twig = new TwigEnv($loader);
$twig->addExtension(new StringExtension());
$twig->addExtension(new \Twig\Extension\DebugExtension());

echo "\nSetup ready in " . (microtime(true) - $start) . " seconds\n";

//STATIC content - copy
foreach($staticDirs as $dir) {
    $src = SOURCE_ROOT . '/' . $dir . '/';
    $dest = $outputDir . $dir . '/';
    if(DIRECTORY_SEPARATOR == "/") {
        //Linux
        //$dest = $outputRoot . '/' . $outputDir . '/' . $dir . '/';
        $dest = $outputDir . $dir . '/';
    }
    $fileSystem = new Symfony\Component\Filesystem\Filesystem();
    $fileSystem->mirror($src, $dest);
}
foreach($staticFiles as $file) {
    $srcFile = SOURCE_ROOT . '/' . $file;
    $destFile = $outputDir . $file;

    //have to check if the destination folder exists before copying
    //if not, create it.
    $destPath = pathinfo($destFile);
    if (!file_exists($destPath['dirname'])) {
        mkdir($destPath['dirname']);
    }
    copy($srcFile, $destFile);
}
echo "\nStatic copied in " . (microtime(true) - $start) . " seconds\n";


$pageMenus = []; //for menu items in front matter
$collections = [];
//MARKDOWN - read files in source directories
foreach($sourceDirs as $src) {
    $mergedJsonStrings = mergedJsonStrings($src,SOURCE_ROOT);
    $collections[$src] = [];
    readMarkdownFiles(SOURCE_ROOT, $src, $outputDir, $converter, $mergedJsonStrings, $collections, $pageMenus);
}
echo "\nMarkdown files read in " . (microtime(true) - $start) . " seconds\n";

//Add position element to each menu item $menus based on key, if it doesn't exist
foreach($yamlMenus as $key1 => $menu) {
    foreach($menu as $key2 => $menuItem) {
        $yamlMenus[$key1][$key2]['position'] = $key2 + 1;
    }  
}
$mergedMenus = $yamlMenus;

//merge the two arrays into one
foreach($pageMenus as $key1 => $menu) {
    if(array_key_exists($key1, $mergedMenus)) {
        $mergedMenus[$key1] = array_merge($mergedMenus[$key1],$menu);
    }
    else {
        $mergedMenus[$key1] = $menu;
    }
}

//reorder mergedMenus items based on their position
foreach($mergedMenus as $key1 => $menu) {
    array_multisort(array_map(function($element) {
        if(array_key_exists("position", $element)) {
            return $element['position'];
        }
        else {
            return 0;
        }
    }, $menu), SORT_ASC, $menu);   
    $mergedMenus[$key1] = $menu;
}

//reorder the collections by date desc
foreach($collections as $key => $collection) {
    if($key != "tags") {
        array_multisort(array_map(function($element) {
            if(array_key_exists("date", $element)) {
                return $element['date'];
            }
            else {
                return 0;
            }
        }, $collection), SORT_DESC, $collection);     
        $collections[$key] = $collection;
    }
}

//reorder tag entries by date desc (collections.tags)
foreach($collections['tags'] as $key => $collection) {
    array_multisort(array_map(function($element) {
        return $element['date'];
    }, $collection), SORT_DESC, $collection);
    
    $collections['tags'][$key] = $collection;    
}

// then loop through each source collection to determine prev/next
foreach($collections as $key => $collection) {
    if($key != "tags" && $key != "pages") {
        //TODO determinePrevNext();
        $prevPagePermalink = "";
        $prevPageTitle = "";
        $nextPagePermalink = "";
        $nextPageTitle = "";
        $index = 0;

        $keys = array_keys($collection);
        $length = count($keys);
        foreach(array_keys($keys) AS $k ){
            $collection[$keys[$k]]["nextPagePermalink"] = $nextPagePermalink;
            $collection[$keys[$k]]["nextPageTitle"] = $nextPageTitle;
            $nextPagePermalink = $collection[$keys[$k]]["permalink"];
            $nextPageTitle = "";
            if (array_key_exists("title",$collection[$keys[$k]])){
                $nextPageTitle = $collection[$keys[$k]]["title"];
            }

            if($k+1 < $length) {
                $collection[$keys[$k]]["prevPagePermalink"] = $collection[$keys[$k+1]]["permalink"];
                $collection[$keys[$k]]["prevPageTitle"] = "";
                if (array_key_exists("title",$collection[$keys[$k+1]])){
                    $collection[$keys[$k]]["prevPageTitle"] = $collection[$keys[$k+1]]["title"];
                }
            }
        }
        $collections[$key] = $collection;
    }
}
echo "\nArrays reordered in " . (microtime(true) - $start) . " seconds\n";

processMarkdown($outputDir, $converter, $twig, $metadata, $mergedMenus, $collections);

//Output collections array to file on site for easier debuggin
echo "\nCollection outputted to: " . $outputDir . "collections.txt\n";
file_put_contents($outputDir . 'collections.txt', print_r($collections, true));
file_put_contents($outputDir . 'collections.txt', print_r("\n=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("METADATA=====================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r($metadata, true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("\n=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("YAML MENUS===================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r($yamlMenus, true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("\n=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("PAGE MENUS===================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r($pageMenus, true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("\n=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("MERGED MENUS=================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r("=============================================================\n", true),FILE_APPEND);
file_put_contents($outputDir . 'collections.txt', print_r($mergedMenus, true),FILE_APPEND);

//remove temporary folder once finished processing
$tempFileSystem = new Symfony\Component\Filesystem\Filesystem();
$tempFileSystem->remove(TEMP_FOLDER);

echo "\nConversion completed in " . (microtime(true) - $start) . " seconds\n";

$endMem = round(memory_get_usage()/1048576,2); 
echo "\nMemory Consumption is $endMem MB\n";
echo "\nDifference: " . ($endMem - $startMem) . "\n";

// https://stackoverflow.com/questions/251277/sorting-php-iterators
// get (recursively) files matching a pattern, each file as SplFileInfo object
function arrayFilterByExtension($a, $ext){
    return array_filter($a, function($obj) use ($ext) {
        return str_ends_with($obj->getFilename(),$ext);
    });
}

function sortByFolderDepth($a, $b) {
    if (substr_count($a->getRealPath(),DIRECTORY_SEPARATOR)  > substr_count($b->getRealPath(),DIRECTORY_SEPARATOR)) {
        return 1;
    } elseif (substr_count($a->getRealPath(),DIRECTORY_SEPARATOR) < substr_count($b->getRealPath(),DIRECTORY_SEPARATOR)) {
        return -1;
    }
    return 0;
}

function getFileContentFromIteratorArrayByType($iter, $ext){
    $result = arrayFilterByExtension($iter, '.' . $ext);
    //sort by slash count in path
    uasort($result, 'sortByFolderDepth');
    return $result;
}

function mergeJsonFilesAsStrings($jsonFiles){
    // loop through files array (SplFileInfo)
    // create basic JSON string array with path as the key
    $jsonStrings = [];
    foreach($jsonFiles as $file){
        $jsonStrings[str_replace('\\','/',$file->getPath())] = file_get_contents($file->getPathname());
        //echo $jsonStrings[$file->getPath()] . "\n";
    }
     
    //loop through json strings array
    //create merged array where merge lower level (closer to the root) json files into higher level (closer to the leaf) 
    $mergedJsonStrings = [];
    foreach($jsonStrings as $path => $curJson){
        $newJson = json_decode($curJson, true);
        //if there are previous entries in the merged array
        if($mergedJsonStrings) {
            //filter all previously processed paths which match the start of the currrent path
            $result = array_filter($mergedJsonStrings, function($key) use ($path) {
                return strpos($path, $key) === 0;
            }, ARRAY_FILTER_USE_KEY);
            //then pop off the last value and convert to json 
            $prevJson = json_decode(array_pop($result), true);
            $curJson = json_decode($curJson, true);
            //then replace the current string into previous string
            $newJson = array_replace_recursive($prevJson, $curJson);
        }
        $mergedJsonStrings[$path] = json_encode($newJson);
    }
    return $mergedJsonStrings;
}

function mergedJsonStrings($sourceDir, $sourceRoot) {
    $matches = new RegexIterator(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceRoot . '/' . $sourceDir)
        ),
        '/(\.json)$/i'
    );
    $files = iterator_to_array($matches);
    $jsonFiles = getFileContentFromIteratorArrayByType($files, 'json');
    return mergeJsonFilesAsStrings($jsonFiles);
}

function readMarkdownFiles($sourceRoot, $sourceDir, $outputDir, $converter, $mergedJsonStrings, &$completeCollection, &$pageMenus) {
    $sourceFullPath = $sourceRoot . '/' . $sourceDir . '/';
    $rdi = new RecursiveDirectoryIterator($sourceFullPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $rii = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST);

    $collection = [];
    foreach ($rii as $file) {
        // store all item info an array to return to the main function for later use
        if ($file->isFile() && $file->getExtension() === 'md') {
            //trying to get the path of the file from root folder (eg "archive/archive.md")
            //first get the real path of the of the file, then remove the real path of the sourceFullPath
            //this ends up with just the filename / path above the sourceDir eg just archive.md
            //so we add that back in again to get "archive/archive.md"
            $path = str_replace('\\','/',$sourceDir . str_replace(realpath($sourceFullPath),'',realpath($file->getPathname())));

            $markdownContent = file_get_contents(str_replace($sourceDir . '/', '', $sourceFullPath) . $path);
            
            //if($path == "docs/docs.md") {
            //echo "\n" . $markdownContent . "\n";
            //}
            
            $result = $converter->convert($markdownContent);

            $frontMatter = [];
            if ($result instanceof RenderedContentWithFrontMatter) {
                $frontMatter = $result->getFrontMatter();
            }
            
            $htmlContent = $result->getContent();            

            if($path == "docs/docs.md") {
            echo "\n" . $htmlContent . "\n";
            }

            // this gets around markdown converting " to &quot; in html 
            // which then causes problems with the rendering due to & in the {{ }} statements
            // It means need to wrap twig statements in HTML comments if they include quotes
            // Now: need to include templates in arrayloader rendering in renderTwigArray further down
            // replace <!--{{ with "{{" and }}--> with "}}"
            // also need to account for escaped comments (eg within code section)
            $withComments = array("<!--{{", "}}-->", "<!--{%", "%}-->");
            $withoutComments = array("{{", "}}", "{%", "%}");
            $htmlContent = str_replace($withComments,$withoutComments,$htmlContent);

            //if($path == "docs/docs.md") {
            //echo "\n" . $htmlContent . "\n";
            //}
            
            $permalink = "";
            $template = 'base.html.twig';

            //get any json based config, if any
            if($mergedJsonStrings){
                //filter processed json strings which match the start of the currrent path
                $relativePath = $sourceRoot . '/' . $path;
                $result = array_filter($mergedJsonStrings, function($key) use ($relativePath) {
                            return strpos($relativePath, $key) === 0;
                }, ARRAY_FILTER_USE_KEY);
                //then pop off the last value and convert to object 
                $json = json_decode(array_pop($result), true);
                $frontMatter = array_merge($json,$frontMatter);

                //merge any tags from json files into frontmatter
                if(array_key_exists('tags',$json)){
                    $tags = $json['tags'];
                    if(!is_array($tags)){
                        $tags = [$tags];
                    }
                    if(!is_array($frontMatter['tags'])){
                        $frontMatter['tags'] = [$frontMatter['tags']];
                    }
                    //add tags from json files
                    $frontMatter['tags'] = array_unique(array_merge($frontMatter['tags'], $tags));
                }
                
                //merge templates if needed
                if(array_key_exists('template',$frontMatter) && strlen($frontMatter['template']) > 0) {
                    $template = $frontMatter['template'];
                }
                else if(array_key_exists("template", $json)){
                    $template = $json["template"];
                }
                $frontMatter['template'] = $template;
            }

            if(array_key_exists("permalink",$frontMatter)){
                $permalink = $frontMatter["permalink"];
            } 
            
            //add extension to template filename
            $frontMatter['template'] = $frontMatter['template'] . '.html.twig';
            
            //if not permalink set in fronmatter 
            //then make the permalink the actual path to the page
            $relativePath = str_replace($sourceDir, '', $path);
            $pageUrl = '/' . $sourceDir . str_replace('.md', '', $relativePath) . '/';
            if(strlen($permalink) > 0){
                $pageUrl = $permalink;
            }
            $pageUrl = str_replace("\\", "/", $pageUrl);
            if(!array_key_exists("permalink",$frontMatter)) { 
                $frontMatter["permalink"] = $pageUrl;
            }

            //ensure permalink starts and ends with a slash
            if(substr($frontMatter["permalink"],0,1) != "/"){
                $frontMatter["permalink"] = "/" . $frontMatter["permalink"];
            }
            
            //ensure permalink starts and ends with a slash
            //if it is not a file with html or xml extensions (other file types not allowed)
            if(!((substr($frontMatter["permalink"],-5) == ".html") 
                || (substr($frontMatter["permalink"],-4) == ".htm")
                || (substr($frontMatter["permalink"],-4) == ".xml"))){
                if(substr($frontMatter["permalink"],-1) != "/"){
                    $frontMatter["permalink"] = $frontMatter["permalink"] . "/";
                }
            }

            $frontMatter['inputPath'] = $path;

            //check title provided, if not add missing title
            if (!array_key_exists("title",$frontMatter)) {
                //if not, put date in the future so you spot it...
                $frontMatter['title'] = "[MISSING TITLE]";
            }

            /*
            ****************************
            ** NOT WORKING ON GITHUB
            ****************************/
            //check date is valid or no date set for posts
            //Note: getfrontmatter converts date strings to their int value automatically. So need to check for valid int
            //echo "\n";
            //echo $frontMatter['date'];
            if (!array_key_exists("date",$frontMatter)) {
                //if no date in the future so you spot it...
                //echo "\n";
                //echo "<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<";
                $frontMatter['date'] = strtotime("9999-12-31 23:59:59");
            }
            else if (!is_int($frontMatter['date'])) {
                /******************************************************************************/
                //On github actions dates are returned as string
                //On my machine they are returned as ints, unless they enclosed in ""
                //So need to cope with both situations.
                //check if string can be converted to date
                //if not put the date in the future
                // See this SO answer: https://stackoverflow.com/a/11029851
                // Should not have Z in the date. It should be 0

                // remove timzone from frontmatter date
                $thedate = substr($frontMatter['date'],0,19);
                if (!(date('Y-m-d H:i:s', strtotime($thedate)) == $thedate)
                    && !(date('Y-m-d H:i:s Z', strtotime($thedate)) == $thedate)) {
                    $frontMatter['date'] = strtotime("9999-12-31 23:59:59");
                }
            }
            
            $tmplVars = $frontMatter;
            $tmplVars['content'] = $htmlContent;
            // this variable is needed so the {{ filename }} command can be used in the permalink value eg in pages.json
            $tmplVars['filename'] = $file->getBasename('.' . $file->getExtension());

            $isDraft = false;
            if(array_key_exists('draft',$frontMatter)){
                if($frontMatter['draft'] == true) {
                    $isDraft = true;
                }
            }

            //only add to collection if not a draft
            if(!$isDraft) {
                //if not draft add to menus array
                if(array_key_exists('navigation',$frontMatter)) {
                    $navigation = $frontMatter['navigation'];
                    if(!is_array($navigation)){
                        $navigation = [$navigation];
                    }
                    $navigation['link'] = $frontMatter['permalink'];
                    //if not title or title is empty, use the page title
                    if(!array_key_exists('title',$navigation) || strlen($navigation['title']) < 1) {
                        $navigation['title'] = $frontMatter['title'];
                    }
                    //default to header menu if no menu defined
                    if(!array_key_exists('menu',$navigation) || strlen($navigation['menu']) < 1) {
                        $navigation['menu'] = 'header';
                    }
                    //default to position 0 if not defined
                    if(!array_key_exists('position',$navigation) || strlen($navigation['position']) < 1) {
                        $navigation['position'] = 0;
                    }

                    //process any twig statements in menu item values 
                    $navigation = renderTwigArray($navigation,$tmplVars);

                    if(!array_key_exists($navigation['menu'],$pageMenus)) {
                        $pageMenus[$navigation['menu']] = [];
                    }
                    array_push($pageMenus[$navigation['menu']],$navigation); 
                    
                    //echo "\n";
                    //var_dump($pageMenus);
                }

                //create a collection entry for each tag
                if(array_key_exists("tags", $frontMatter)){
                    foreach($frontMatter['tags'] as $tag){
                        //remove content element
                        $tagVars = $tmplVars;
                        unset($tagVars['content']); //don't need content in this collection
                        unset($tagVars['tags']); //don't need tags array in this collection
                        $completeCollection['tags'][$tag][$path] = $tagVars;
                    }
                }
                $completeCollection[$sourceDir][$path] = $tmplVars;
                $collection[$path] = $frontMatter;
            }
        }
    }
}

function renderTwigArray($arrayToChange,$values){
    foreach ($arrayToChange as $key => $value) 
    {
        if (is_string($value)) {
            //'index' is the name of the template to use and it is set equal to  the content of $value
            //ie the template is the content of the $value string, which could be a markdown file for example
            $arrayLoader = new \Twig\Loader\ArrayLoader([
                'index' => $value,
            ]);

            $fsLoader = new FilesystemLoader(TEMP_FOLDER);

            $chainLoader = new \Twig\Loader\ChainLoader([$arrayLoader, $fsLoader]);
            
            $arrayTwig = new \Twig\Environment($chainLoader);
            $arrayTwig->addExtension(new StringExtension());
            $arrayTwig->addExtension(new \Twig\Extension\DebugExtension());
            $arrayToChange[$key] = $arrayTwig->render('index', $values);
        }
    }
    return $arrayToChange;
}

function renderTwig($twig, $outputDir, $tmplVars){
    // Process Twig statements inside tmplVars
    $tmplVars = renderTwigArray($tmplVars,$tmplVars);
    $outputPath =  $outputDir . $tmplVars['permalink'];

    //if output path (ie permalink) ends with a / add index.html, 
    //else it's a file so don't change
    //    eg "/some.folder/" should be "/some.folder/index.html", 
    //    but "/sitemap.xml" should remain "/sitemap.xml"
    if(!strpos($outputPath,".", (is_null(strrpos($outputPath, "/"))?0:strrpos($outputPath, "/")))){
        $outputPath = $outputPath . 'index.html'; 
    }

    if (!is_dir(dirname($outputPath))) {
        mkdir(dirname($outputPath), 0777, true);
    }
    file_put_contents($outputPath, $twig->render($tmplVars["template"], $tmplVars));
}

function processPagination($twig, $outputDir, $tmplVars, $completeCollection){
    $data = $tmplVars['pagination']['data'];
    $alias = 'posts';
    /* alias is a left over from eleventy enabling to target this collection of items 
             using a specific name for each collection in the templates
             You could then treat each collection differently in the templates
             It is not used in this engine.*/
    /*
    if($tmplVars['pagination']['alias'] && strlen($tmplVars['pagination']['alias']) > 0){
        $alias = $tmplVars['pagination']['alias'];
    }
    */
    $size = 0; //Fix to all on one page
    $tmplVars['pagination']['total'] = count($completeCollection['tags'][$data]);
    $tmplVars[$alias] = $completeCollection['tags'][$data];
    $tmplVars['pagination']['pages'] = 1;
    $tmplVars['pagination']['current'] = 1;
    renderTwig($twig, $outputDir, $tmplVars);
    
    /* Uncomment this if want proper paging
     * And comment out the above code
    if(array_key_exists('size',$tmplVars['pagination']) && $tmplVars['pagination']['size'] && strlen($tmplVars['pagination']['size']) > 0){
        $size = $tmplVars['pagination']['size'];
    }

    if($size == 0 || $size > count($completeCollection['tags'][$data])){
        //echo $tmplVars['title'] . " - processPagination - no paging \n";
        //no pages
        $tmplVars[$alias] = $completeCollection['tags'][$data];
        $tmplVars['pagination']['pages'] = 1;
        $tmplVars['pagination']['current'] = 1;

        renderTwig($twig, $outputDir, $tmplVars);
    }
    else {
        // paging - need to slice up data into chunks and loop through it.
        // set $tmplVars[$alias] to the correct slice
        // set URL to include page number
        $chunks = array_chunk($completeCollection[$data], $size);
        $page = 1;
        $tmplVars['pagination']['pages'] = count($chunks);

        foreach($chunks as $chunk){
            //echo $tmplVars['title'] . " - processPagination - chunk - $page \n";
            $tmplVars[$alias] = $chunk;
            $realPermalink = $tmplVars["permalink"];  
            $tmplVars["permalink"] = $tmplVars["permalink"] . $page . "/";
            $tmplVars['pagination']['current'] = $page;

            renderTwig($twig, $outputDir, $tmplVars);

            //reset permalink back to original, so don't get chaining of page numbers: /archive/1/2/3/
            $tmplVars["permalink"] = $realPermalink;
            $page++;
        }
    }
    */
}

function processMarkdown($outputDir, $converter, $twig, $metadata, $menus, $completeCollection) {
    echo "\nProcessing Markdown files to output dir: " . $outputDir . "\n";
    //sort tags array by key
    ksort($completeCollection['tags'],SORT_NATURAL | SORT_FLAG_CASE);
    foreach($completeCollection as $srcKey => $src) {
        foreach($src as $key => $item) {

            //don't handle "tags" source as that is not a real source, and is handled through pagination
            if($srcKey != "tags") {
                $tmplVars = $item;
                $tmplVars['metadata'] =  $metadata; 
                $tmplVars['menus'] =  $menus; 
                $tmplVars['collections'] =  $completeCollection; 

                //check if there is pagination object and data object
                if(array_key_exists('pagination',$tmplVars) && array_key_exists('data',$tmplVars['pagination'])){
                    // for pages listing posts per tag need to loop through this section, eg if pagination data = "tags"???
                    $data = $tmplVars['pagination']['data'];
                    if($data == "tag") {
                        foreach($tmplVars['collections']['tags'] as $key => $collection) {
                            // Setting up Slugger
                            $slugger = new AsciiSlugger(); // you can type-hint SluggerInterface to get slugger as a service
                            $slug = $slugger->slug($key)->lower();                        

                            $tmplVars['template'] = 'tagged.html.twig';
                            $tmplVars['pagination']['data'] = $key;
                            $tmplVars['pagination']['size'] = 0;
                            $tmplVars['alias'] = $key;
                            $tmplVars['permalink'] = '/tagging/' . $slug . '/';
                            $tmplVars['content'] = "Posts for tag: " . $key;
                            processPagination($twig, $outputDir, $tmplVars, $completeCollection);
                        }
                    }
                    else {
                        processPagination($twig, $outputDir, $tmplVars, $completeCollection);
                    }
                }
                else {
                    renderTwig($twig, $outputDir, $tmplVars);
                }
            }
        }
    }
}
?>
