<?php
namespace Core\Language;

/**
 * Text.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016
 * @license MIT
 */
class Text implements TextInterface
{

    /**
     *
     * @var string
     */
    private $storage_name;

    /**
     *
     * @var LanguageInterface
     */
    private $language;

    /**
     *
     * {@inheritdoc}
     *
     * @see \Core\Language\TextInterface::setLanguage($language)
     */
    public function setLanguage(LanguageInterface $language)
    {
        $this->language = $language;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Core\Language\TextInterface::setStorageName($storage_name)
     */
    public function setStorageName(string $storage_name)
    {
        $this->storage_name = $storage_name;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Core\Language\TextInterface::get($key, $strings)
     */
    public function get(string $key, array $strings = []): string
    {
        if (!isset($this->language)) {
            Throw new LanguageException('Text neeeds a set Language object.');
        }

        if (!isset($this->storage_name)) {
            Throw new LanguageException('No storage name set.');
        }

        $text = $this->language->get($this->storage_name, $key);

        if (!empty($strings)) {
            $text = vsprintf($text, $strings);
        }

        return $text;
    }
}
