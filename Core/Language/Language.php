<?php
namespace Core\Language;

use function Core\arrayFlatten;

/**
 * Language.php
 *
 * @author Michael "Tekkla" Zorn <tekkla@tekkla.de>
 * @copyright 2016-2017
 * @license MIT
 */
class Language implements LanguageInterface
{

    /**
     *
     * @var LanguageStorage
     */
    private $storage;

    /**
     *
     * @var string
     */
    private $fallback = '';

    /**
     * Constructor
     *
     * Inits the internel storage.
     */
    public function __construct()
    {
        $this->storage = new LanguageStorage();
    }

    /**
     * Sets the name of the fallback storage
     *
     * The fallback storage will be queried for a text when the requested storage returns nor result.
     *
     * @param string $fallback
     */
    public function setFallbackStorageName(string $fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * Loads a language file
     *
     * When a text already exists in a storage, the loaded texts overwrites and extends the storage with the new data.
     *
     * @param string $storage_name
     *            Name of storage the loaded language texts will be stored in
     * @param string $filename
     *            File path of mthe languagefile to load
     */
    public function loadLanguageFile(string $storage_name, string $filename)
    {
        if (file_exists($filename)) {

            $lang_array = include ($filename);

            if (is_array($lang_array)) {

                $lang_array = arrayFlatten($lang_array, '', '.', true);

                foreach ($lang_array as $key => $val) {

                    if (!isset($this->storage->{$storage_name})) {
                        $this->storage->{$storage_name} = new LanguageStorage();
                    }

                    $this->storage->{$storage_name}->{$key} = $val;
                }
            }
        }
    }

    /**
     * Returns text form by key from a storage
     *
     * Only translates texts that does not contain spaces.
     * Tries to find text in optional set fallback storage when key is not found in the requested one.
     * Returns the key when no text is found.
     * Allows linking of keys within a storage and throws an exception when it comes to ininite loops.
     *
     * @param string $storage_name
     *            Name of storage to query for the key belongs to.
     * @param string $key
     *            Key of the requested text
     *
     * @throws LanguageException
     *
     * @return string
     */
    public function get(string $storage_name, string $key)
    {

        // IMPORTANT! Keys with spaces won't be processed without any further
        // notice to the developer or user. Spaces mean texts and no keys.
        if (is_array($key) || strpos($key, ' ')) {
            return $key;
        }

        // Return key when key is not found
        if (!isset($this->storage->{$storage_name}->{$key})) {

            // Do we have a fallback storage set to look for?
            if (!empty($this->fallback) && isset($this->storage->{$this->fallback})) {

                if (isset($this->storage->{$this->fallback}->{$key})) {
                    return $this->storage->{$this->fallback}->{$key};
                }

                return $key;
            }
        }

        $text = $this->storage->{$storage_name}->{$key};

        // Prevent infinite loops
        if ($text == $key) {
            Throw new LanguageException(sprintf('There is an infinite loop recursion in language data of storage "%s" on key "%s"', $storage_name, $key));
        }

        // Return requested text
        return $this->get($storage_name, $text);
    }

    /**
     * Creates and return as TextAdapter object
     *
     * @param string $storage_name
     *            Name of the text storage the adapter gets it's data from
     * @return \Core\Language\Text
     */
    public function createTextAdapter($storage_name)
    {
        $adapter = new Text();
        $adapter->setLanguage($this);
        $adapter->setStorageName($storage_name);

        return $adapter;
    }
}
