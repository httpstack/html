<?php
namespace HttpStack\Template;

use DOMXPath;
use DOMElement;
use DomDocument;
use HttpStack\IO\FileLoader;
use HttpStack\App\Models\TemplateModel;
/*
1.0.0: The initial class that handled basic variable replacement ({{var}}).

1.1.0: A minor version bump for adding a significant, backward-compatible feature: the ability to define and use functions ({{func()}}).

1.1.1: A patch version bump for fixing the bug where numeric literals in function arguments were not parsed correctly.

1.2.0: The current version. This is another minor version bump because we added a major new feature: support for multiple, namespaced templates and the {{include()}} function for composition.
*/

/**
 * A straightforward template engine for replacing placeholders and executing functions.
 */


/**
 * A straightforward template engine for replacing placeholders and executing functions.
 * Now with support for multiple namespaced templates.
 *
 * @version 1.2.0
 */
class Template
{
    /**    
     * The version of the SimpleTemplate class.
     */
    const VERSION = '1.2.0';

    /**
     * @var array Holds the content of all loaded templates, keyed by namespace.
     */
    private array $arrTemplates = [];

    /**
     * @var array An associative array of variables for replacement.
     */
    private array $arrVariables = [];

    /**
     * @var array An associative array of user-defined functions.
     */
    private array $arrFunctions = [];

    /**
     * Constructor for the SimpleTemplate class.
     *
     * @param string $strMainTemplatePath The absolute path to the main template file.
     * @param array  $arrInitialData Optional initial data to set.
     * @throws \Exception If the template file is not found or readable.
     */
    public function __construct(string $strMainTemplatePath, array $arrInitialData = [])
    {
        // Load the main template into the 'main' namespace by default.
        $this->addTemplate('main', $strMainTemplatePath);
        $this->setAll($arrInitialData);

        // Pre-define an 'include' function to allow rendering partials from within templates.
        $this->define('include', function (string $strNamespace) {
            // This calls the render method internally for the specified partial.
            return $this->render($strNamespace);
        });
    }

    /**
     * Adds a template from a file path to the internal collection with a given namespace.
     *
     * @param string $strNamespace The name to refer to this template by.
     * @param string $strFilePath The absolute path to the template file.
     * @return self
     * @throws \Exception If the file is not found or readable.
     */
    public function addTemplate(string $strNamespace, string $strFilePath): self
    {
        if (!file_exists($strFilePath) || !is_readable($strFilePath)) {
            throw new \Exception("Template file not found or is not readable: {$strFilePath}");
        }
        $this->arrTemplates[$strNamespace] = file_get_contents($strFilePath);
        return $this;
    }

    /**
     * Sets a single replacement variable.
     *
     * @param string $strKey The name of the variable (placeholder).
     * @param mixed  $mixValue The value to replace the placeholder with.
     * @return self
     */
    public function set(string $strKey, $mixValue): self
    {
        $this->arrVariables[$strKey] = $mixValue;
        return $this;
    }

    /**
     * Sets multiple replacement variables from an associative array.
     *
     * @param array $arrData An associative array of variables.
     * @return self
     */
    public function setAll(array $arrData): self
    {
        $this->arrVariables = array_merge($this->arrVariables, $arrData);
        return $this;
    }

    /**
     * Removes a replacement variable.
     *
     * @param string $strKey The key of the variable to remove.
     * @return self
     */
    public function remove(string $strKey): self
    {
        unset($this->arrVariables[$strKey]);
        return $this;
    }

    /**
     * Defines a function that can be called from within the template.
     *
     * @param string   $strName     The name of the function.
     * @param callable $calCallback The function/closure to execute.
     * @return self
     */
    public function define(string $strName, callable $calCallback): self
    {
        $this->arrFunctions[$strName] = $calCallback;
        return $this;
    }

    /**
     * Renders a specific namespaced template.
     *
     * @param string $strNamespace The namespace of the template to render. Defaults to 'main'.
     * @return string The rendered content.
     * @throws \Exception If the requested template namespace does not exist.
     */
    public function render(string $strNamespace = 'main'): string
    {
        if (!isset($this->arrTemplates[$strNamespace])) {
            throw new \Exception("Template with namespace '{$strNamespace}' not found.");
        }

        $strTemplateContent = $this->arrTemplates[$strNamespace];
        $self = $this; // Create a reference for use inside the closure

        $strPattern = '/{{\s*([a-zA-Z0-9_]+)(?:\((.*?)\))?\s*}}/';

        $strRenderedHtml = preg_replace_callback($strPattern, function ($arrMatches) use ($self) {
            $strName = $arrMatches[1]; // The name of the variable or function.

            if (isset($arrMatches[2])) {
                $strArgsString = $arrMatches[2];
                if (isset($self->arrFunctions[$strName])) {
                    $arrParams = $self->parseArguments($strArgsString);
                    return call_user_func_array($self->arrFunctions[$strName], $arrParams);
                }
                return '';
            }
            return $self->arrVariables[$strName] ?? '';
        }, $strTemplateContent);

        return $strRenderedHtml;
    }

    /**
     * Parses the argument string from a function call in the template.
     * @internal
     */
    public function parseArguments(string $strArgsString): array
    {
        if (trim($strArgsString) === '') {
            return [];
        }

        $arrArgs = str_getcsv($strArgsString);
        $arrResolvedArgs = [];

        foreach ($arrArgs as $strArg) {
            $strArg = trim($strArg);

            if ((str_starts_with($strArg, "'") && str_ends_with($strArg, "'")) ||
                (str_starts_with($strArg, '"') && str_ends_with($strArg, '"'))) {
                $arrResolvedArgs[] = substr($strArg, 1, -1);
            } elseif (is_numeric($strArg)) {
                $arrResolvedArgs[] = strpos($strArg, '.') === false ? (int)$strArg : (float)$strArg;
            } else {
                $arrResolvedArgs[] = $this->arrVariables[$strArg] ?? null;
            }
        }
        return $arrResolvedArgs;
    }
}


// --- Example Usage ---

/*
// 1. Create a main template file: 'main_layout.html'
// ----------------------------------------------------
// <!DOCTYPE html>
// <html>
// <head>
//     <title>{{page_title}}</title>
// </head>
// <body>
//     {{ include('header') }}
//     <main>
//         <p>{{main_content}}</p>
//     </main>
//     {{ include('footer') }}
// </body>
// </html>

// 2. Create a partial template file: 'header.html'
// --------------------------------------------------
// <header>
//     <h1>Welcome, {{user_name}}!</h1>
// </header>

// 3. Create another partial: 'footer.html'
// ------------------------------------------
// <footer>
//     <p>Copyright {{ year() }}. All rights reserved.</p>
// </footer>


// 4. Use the class in your PHP script
// -------------------------------------
try {
    $strMainTemplatePath = __DIR__ . '/main_layout.html';
    $strHeaderPath = __DIR__ . '/header.html';
    $strFooterPath = __DIR__ . '/footer.html';

    $arrInitialData = [
        'page_title' => 'Multi-Template Example',
        'user_name' => 'John Doe',
        'main_content' => 'This is the main content of the page, loaded into the main layout.'
    ];

    // Initialize with the main layout
    $objTemplate = new SimpleTemplate($strMainTemplatePath, $arrInitialData);

    // Add the partials with their own namespaces
    $objTemplate->addTemplate('header', $strHeaderPath);
    $objTemplate->addTemplate('footer', $strFooterPath);

    // Define a function
    $objTemplate->define('year', function(): string {
        return date('Y');
    });

    // Render the final HTML by calling render() on the main template.
    // The 'include' function will render the partials automatically.
    $strFinalHtml = $objTemplate->render();

    echo $strFinalHtml;

} catch (\Exception $e) {
    die('Error: ' . $e->getMessage());
}
*/
