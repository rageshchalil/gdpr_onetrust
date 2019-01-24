# gdpr-onetrust
OneTrust GDPR Library displays an overlay on the website to make users aware of the fact that cookies are being set.
The user may then give his/her consent or move to a page that provides more details regarding the cookies being dropped on the website.
Consent is given by user accepting the agree with buttons or by continuing browsing the website.
The library uses OnTrust services, so a UUID needs to be procured from OneTrust https://onetrust.com/.


## Installation

1. Run composer install

    ```
    composer install
    ```

## Usage

1. Autoload and include library :
    ```
    define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
    define('VENDOR', ROOT . 'vendor' . DIRECTORY_SEPARATOR);
    require VENDOR . 'autoload.php';
    ```
2. Include the namespace and initialize.
    ```
    use \Gdpr\Gdpr\Gdpronetrust;
    $gdpr = Gdpronetrust::instance();
    ```

3. Identify the categories and its ID's.
    ```
    eg:
        * Strictly Necessary Cookies => 1
        * Performance Cookies => 2
        * Functional Cookies  => 3
        * Targeting Cookies => 4
    ```
4. Use the function to achieve the cookie blocking.
    
    Insertscript:
    ```   
    $gdpr->($js_path, $selector = "head", $callback, $options, $js_group, $inline = FALSE);
     /**
       * @param string $js_path
       *  The javascript file path
       * @param string $selector
       *  The location to load the js 'head', 'body', '<parent id>'
       * @param $callback
       *  A JavaScript function to be called once the <script> tag has been inserted
       * @param $options
       *  A list of behaviours for when the <script> tag is inserted
       * @param $js_group
       *  Group id for which the <script> tag will be inserted
       * @param bool $inline
       *  Set TRUE if js should be loaded inline, ie. outside optanon wrapper.
       * @return string
       */
    ```
    
    Inserthtml:
    
    ```
    $gdpr->optanonInserthtml($element, $selector, $callback, $options, $groupid, $inline = FALSE);
    /**
       * @param $element
       *  Html tag to be inserted
       * @param $selector
       *  Html parent element id where the element will be inserted
       * @param $callback
       *  A javascript function to be called once the element has been inserted
       * @param $options
       *  A list of behaviours for when the element is inserted
       * @param $groupid
       *  Group id for which the element will be inserted.
       * @param bool $inline
       *  Set TRUE if js should be loaded inline, ie. outside optanon wrapper.
       * @return string
    */
    ```

5. Use the function to publish.
    
    ```
    $gdpr->optanonPublishScript();
    ```