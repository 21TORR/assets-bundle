<?php declare(strict_types=1);

namespace Torr\Assets\File\Type\Css;

use Torr\Assets\Routing\AssetUrlGenerator;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Asset\InvalidAssetException;

class CssUrlRewriter
{
    private AssetUrlGenerator $assetUrlGenerator;

    /**
     */
    public function __construct (AssetUrlGenerator $assetUrlGenerator)
    {
        $this->assetUrlGenerator = $assetUrlGenerator;
    }

    /**
     * Find and replace namespaces in $content like url("@app/css/app.css") to url("/assets/app/css/app.css") if "@app/css/app.css" is defined
     */
    public function rewrite (string $content) : string
    {
        return \preg_replace_callback(
            '~url\\(\\s*(?<path>.*?)\\s*\\)~i',
            function (array $match)
            {
                return $this->replaceImport($match);
            },
            $content
        );
    }

    /**
     * Replaces the single import with the resolved path
     */
    private function replaceImport(array $match) : string
    {
        $importPath = \trim($match["path"], " '\"");

        try {
            $asset = Asset::create($importPath);
            return \sprintf('url("%s")', $this->assetUrlGenerator->getUrl($asset));
        }
        catch (InvalidAssetException $exception)
        {
            // if there is an issue -> just return unchanged
            return $match[0];
        }
    }
}
