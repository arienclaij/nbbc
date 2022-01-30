<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Enhanced Tag Example</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        require_once(__DIR__ . "/../vendor/autoload.php");

        use Nbbc\BBCode;

/* A part of a test for a new tag for include a gallery into a parsed input . */
        $gallery_array = array(
            'single' => array(
                1 => 'one.jpg',
                2 => 'two.jpg',
                3 => 'three.jpg',
                4 => 'four.jpg',
                5 => 'five.jpg',
            ),
            'group' => array(
                1 => array(
                    41 => 'set_1_one.jpg',
                    42 => 'set_1_two.jpg',
                    43 => 'set_1_three.jpg',
                ),
                2 => array(
                    51 => 'set_2_one.jpg',
                    52 => 'set_2_two.jpg',
                    53 => 'set_2_three.jpg',
                ),
                3 => array(
                    58 => 'set_3_one.jpg',
                    59 => 'set_3_two.jpg',
                )
            )
        );
        #echo "<pre>".print_r($gallery_array,true)."</pre>";

        $input = "[scroll]This is a scrolling text![/scroll]\n"
                . "[border color=red size=3]This text is in a medium red border![/border]\n"
                . "[border size=10]This text is in a fat blue border![/border]\n"
                . "[border color=green]This text is in a normal green border![/border]\n"
                . "[border]This text is in a normal border![/border]\n"
                . "[gallery type=single]5[/gallery]\n"
                . "[gallery type=group]1[/gallery]\n"
        ;

        $bbcode = new BBCode();

        // See for more information the docs in /doc and navigate to Callback-function

        /* Custom Callback-function for 'MyBorderFunction' */
        function MyBorderFunction($bbcode, $action, $name, $default, $params, $content) {
            if ($action == BBCODE_CHECK) {
                // Validation of parameters starts here
                if (isset($params['color']) && !preg_match('/^#[0-9a-fA-F]+|[a-zA-Z]+$/', $params['color']))
                    return false;
                if (isset($params['size']) && !preg_match('/^[1-9][0-9]*$/', $params['size']))
                    return false;
                // If everything seems okay.
                return true;
            }

            // check whether parameters has been filled in, and otherwise use your own value.
            $color = isset($params['color']) ? $params['color'] : "blue";
            $size = isset($params['size']) ? $params['size'] : 1;
            return "<div style=\"border: {$size}px solid $color\">$content</div>";
        }

        /* Work in progress: Custom Callback-function for 'Gallery' */

        function MyGalleryFunction($bbcode, $action, $name, $default, $params, $content) {
            if ($action == BBCODE_CHECK) {
                // Let's make some checks fot the existing of the array, and return  a false-boolean  if array doesn't exists.
                return true;
            }
            // This is work in progress:
            // hier de content tonen, en afhankelijk van de $params['type'] kijken of het enkele foto is, of een array in group
            // indien group, foreach, output opslaan en doe wat leuks.
            return '<div class="gallery">Soort:' . $params['type'] . $content . "</div>";
        }

        /* The settings for all custom tags */

        // Border
        $border = Array(
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'MyBorderFunction',
            'class' => 'block',
            'allow_in' => Array('listitem', 'block', 'columns'),
        );
        $bbcode->AddRule('border', $border);

        // Gallery
        $gallery = Array(
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'MyGalleryFunction',
            'class' => 'block',
            'allow_in' => Array('block'),
        );
        $bbcode->AddRule('gallery', $gallery);

        // Marquee
        $marquee = Array(
            'simple_start' => '<marquee>',
            'simple_end' => '</marquee>',
            'class' => 'inline',
            'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
        );
        $bbcode->AddRule('scroll', $marquee);

        // Let's parse the UBB-code
        $output = $bbcode->Parse($input);
        print "<div class='bbcode'>" . $output . "</div>";
        ?>
    </body>
</html>
