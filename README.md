#Meta

A package that makes it easy to add meta tags to your views.
This is a fork of Ryan Nelson's original package that does not include any framework-specific code.

This package will work in any PHP application.

## Installation

Run the following Composer command in your terminal, or simply add `"mattyg/meta": "2.0.*"` to your composer.json file:

    composer require mattyg/meta

Then update Composer from the terminal:

    composer update

That's it!


## Usage

To set meta tag values, you will use the `set(array())` method on the Meta instance. Just pass this Meta object around to persist the set values. 

    $meta = new \RyanNielson\Meta\Meta;

    // Example #1 - Basic setting of values
    $meta->set(array("title" => "Page Title", "description" => "Page Description", "keywords" => array("great", "site")));

    // Example #2 - Setting nested values. This will render tags with names like og:title and og:description
    $meta->set(array("title" => "Page Title", "og" => array("title" => "OG Title", "description" => "OG Description")));


To display your meta tags using the set values, you will use the `display(array())` function on your Meta object.:

    $meta->display();

    // Displaying Example #1 from above
    <meta name="title" content="Page Title"/>
    <meta name="description" content="Page Description"/>
    <meta name="keywords" content="great, site"/>

    // Displaying Example #2 from above
    <meta name="title" content="Page Title"/>
    <meta name="og:title" content="OG Title"/>
    <meta name="og:description" content="OG Description"/>


The display function also accepts an array of default values. These will be used when displaying your meta tags if a value is not set already using `set()`.

