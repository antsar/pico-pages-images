<?php

/**
 * Access the images of the current page with {{ images }} in Pico CMS.
 *
 * @author  Nicolas Liautaud
 * @link    http://nliautaud.fr
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 */
final class PicoPagesImages extends AbstractPicoPlugin
{
    private $path;
    private $root;
    private $base;
    
    /**
     * This plugin is enabled by default
     *
     * @see AbstractPicoPlugin::$enabled
     * @var boolean
     */
    protected $enabled = true;

    /**
     * Register page path, relative to Pico content, without index and .md
     *
     * Triggered after Pico has discovered the content file to serve
     *
     * @see    Pico::getBaseUrl()
     * @see    Pico::getRequestFile()
     * @param  string &$file absolute path to the content file to serve
     * @return void
     */
    public function onRequestFile(&$file)
    {
        $this->path = ltrim($file, $this->getPico()->getRootDir());
        $this->path = ltrim($this->path, 'content');
        $this->path = rtrim($this->path, 'index.md');
        $this->path = rtrim($this->path, '.md');
        $this->path = rtrim($this->path, '/') . '/';
    }
    /**
     * Triggered after Pico has read its configuration
     *
     * @see    Pico::getConfig()
     * @param  array &$config array of config variables
     * @return void
     */
    public function onConfigLoaded(array &$config)
    {
        $this->base = rtrim($config['base_url'], '/') . '/';
        if (!empty($config['images_path']))
            $this->root = rtrim($config['images_path'], '/');
        else $this->root = 'images';
    }
    /**
     * Triggered before Pico renders the page
     *
     * @see    Pico::getTwig()
     * @see    DummyPlugin::onPageRendered()
     * @param  Twig_Environment &$twig          twig template engine
     * @param  array            &$twigVariables template variables
     * @param  string           &$templateName  file name of the template
     * @return void
     */
    public function onPageRendering(Twig_Environment &$twig, array &$twigVariables, &$templateName)
    {
        $twigVariables['images'] = $this->images_list();
    }
    /**
     * Return the list and infos of images in the current directory.
     *
     * @return array
     */
    private function images_list()
    {
        $images_path = $this->root . $this->path;

        $data = array();
        $pattern = '*.{[jJ][pP][gG],[jJ][pP][eE][gG],[pP][nN][gG],[gG][iI][fF]}';
        $images = glob($images_path . $pattern, GLOB_BRACE);
        
        foreach( $images as $path )
        {
            list(, $basename, $ext, $filename) = array_values(pathinfo($path));
            list($width, $height, $type, $size, $mime) = getimagesize($path);

            $data[] = array (
                'url' => $this->base . $images_path . $basename,
                'path' => $images_path,
                'name' => $filename,
                'ext' => $ext,
                'width' => $width,
                'height' => $height,
                'size' => $size
            );
        }
        return $data;
    }
}
?>