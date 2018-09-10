<?php namespace Cog;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

abstract class FileSystem {

	/** @var \Symfony\Component\Filesystem\Filesystem */
	private static $fs;

	private static function initialize() {
		if (!self::$fs instanceof \Symfony\Component\Filesystem\Filesystem) {
			self::$fs = new \Symfony\Component\Filesystem\Filesystem();
		}
	}

	/**
	 * Copies a file.
	 * If the target file is older than the origin file, it's always overwritten.
	 * If the target file is newer, it is overwritten only when the
	 * $overwriteNewerFiles option is set to true.
	 *
	 * @param string $originFile The original filename
	 * @param string $targetFile The target filename
	 * @param bool $overwriteNewerFiles If true, target files newer than origin files are overwritten
	 * @throws FileNotFoundException When originFile doesn't exist
	 * @throws IOException           When copy fails
	 */
	public static function copy($originFile, $targetFile, $overwriteNewerFiles = false) {
		self::initialize();
		self::$fs->copy($originFile, $targetFile, $overwriteNewerFiles);
	}

	/**
	 * Creates a directory recursively.
	 *
	 * @param string|array|\Traversable $dirs The directory path
	 * @param int $mode The directory mode
	 * @return void
	 * @throws IOException On any directory creation failure
	 */
	public static function mkdir($dirs, $mode = 0777) {
		self::initialize();
		self::$fs->mkdir($dirs, $mode);
	}

	/**
	 * Checks the existence of files or directories.
	 *
	 * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to check
	 * @return bool true if the file exists, false otherwise
	 */
	public static function exists($files) : bool {
		self::initialize();
		return self::$fs->exists($files);
	}

	/**
	 * Sets access and modification time of file.
	 *
	 * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to create
	 * @param int $time The touch time as a Unix timestamp
	 * @param int $atime The access time as a Unix timestamp
	 * @throws IOException When touch fails
	 */
	public static function touch($files, $time = null, $atime = null) {
		self::initialize();
		self::$fs->touch($files, $time, $atime);
	}

	/**
	 * Removes files or directories.
	 *
	 * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to remove
	 * @throws IOException When removal fails
	 */
	public static function remove($files) {
		self::initialize();
		self::$fs->remove($files);
	}

	/**
	 * Change mode for an array of files or directories.
	 *
	 * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to change mode
	 * @param int $mode The new mode (octal)
	 * @param int $umask The mode mask (octal)
	 * @param bool $recursive Whether change the mod recursively or not
	 * @throws IOException When the change fail
	 */
	public static function chmod($files, $mode, $umask = 0000, $recursive = false) {
		self::initialize();
		self::$fs->chmod($files, $mode, $umask, $recursive);
	}

	/**
	 * Change the owner of an array of files or directories.
	 *
	 * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to change owner
	 * @param string $user The new owner user name
	 * @param bool $recursive Whether change the owner recursively or not
	 * @throws IOException When the change fail
	 */
	public static function chown($files, $user, $recursive = false) {
		self::initialize();
		self::$fs->chown($files, $user, $recursive);
	}

	/**
	 * Change the group of an array of files or directories.
	 *
	 * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to change group
	 * @param string $group The group name
	 * @param bool $recursive Whether change the group recursively or not
	 * @throws IOException When the change fail
	 */
	public static function chgrp($files, $group, $recursive = false) {
		self::initialize();
		self::$fs->chgrp($files, $group, $recursive);
	}

	/**
	 * Renames a file or a directory.
	 *
	 * @param string $origin The origin filename or directory
	 * @param string $target The new filename or directory
	 * @param bool $overwrite Whether to overwrite the target if it already exists
	 * @throws IOException When target file or directory already exists
	 * @throws IOException When origin cannot be renamed
	 */
	public static function rename($origin, $target, $overwrite = false) {
		self::initialize();
		self::$fs->rename($origin, $target, $overwrite);
	}

	/**
	 * Given an existing path, convert it to a path relative to a given starting path.
	 *
	 * @param string $endPath Absolute path of target
	 * @param string $startPath Absolute path where traversal begins
	 * @return string Path of target relative to starting path
	 */
	public static function makePathRelative($endPath, $startPath) : string {
		self::initialize();
		return self::$fs->makePathRelative($endPath, $startPath);
	}

	/**
	 * Returns whether the file path is an absolute path.
	 *
	 * @param string $file A file path
	 * @return bool
	 */
	public static function isAbsolutePath($file) : bool {
		self::initialize();
		return self::$fs->isAbsolutePath($file);
	}

	/**
	 * Mirrors a directory to another.
	 *
	 * @param string $originDir The origin directory
	 * @param string $targetDir The target directory
	 * @param \Traversable $iterator A Traversable instance
	 * @param array $options An array of boolean options
	 *                   Valid options are:
	 *                   - $options['override'] Whether to override an existing file on copy or not (see copy())
	 *                   - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink())
	 *                   - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
	 * @throws IOException When file type is unknown
	 */
	public static function mirror($originDir, $targetDir, \Traversable $iterator = null, array $options = []) {
		self::initialize();
		self::$fs->mirror($originDir, $targetDir, $iterator, $options);
	}

	/**
	 * Creates a temporary file with support for custom stream wrappers.
	 *
	 * @param string $dir The directory where the temporary filename will be created.
	 * @param string $prefix The prefix of the generated temporary filename.
	 *                       Note: Windows uses only the first three characters of prefix.
	 * @return string The new temporary filename (with path), or throw an exception on failure.
	 */
	public static function tempnam($dir, $prefix) : string {
		self::initialize();
		return self::$fs->tempnam($dir, $prefix);
	}

	/**
	 * Atomically dumps content into a file.
	 *
	 * @param string $filename The file to be written to.
	 * @param string $content The data to write into the file.
	 * @throws IOException If the file cannot be written to.
	 */
	public static function dumpFile($filename, $content) {
		self::initialize();
		self::$fs->dumpFile($filename, $content);
	}

	/**
	 * Remove anything which isn't a word, whitespace, number or any of the following characters -_~,;:[]().
	 *
	 * @param $filename
	 * @return mixed
	 */
	public static function sanitizeFilename($filename) {
		$filename = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $filename);
		$filename = preg_replace("([\.]{2,})", '', $filename); // Remove any runs of periods
		return $filename;
	}

	/**
	 * Gets mime information about a file
	 * @param string $filePath path to examined file
	 *
	 * @throws \LogicException
	 * @throws \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
	 * @throws \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException
	 * @return string file's mime type, empty string if cannot detect
	 */
	public static function getMimeType($filePath) : string {
		$toReturn = '';

		$mimeGuesser = MimeTypeGuesser::getInstance();
		$mime = $mimeGuesser->guess($filePath);

		if ($mime !== '' && $mime !== false) {
			$toReturn = $mime;
		}

		return $toReturn;
	}

	/**
	 * Removes all files from a given directory
	 * @param string $directoryPath
	 * @param array $filesNamesToOmit
	 *
	 * @return int mount of files removed
	 */
	public static function cleanDirectory($directoryPath, array $filesNamesToOmit = []) : int {
		$count = 0;

		foreach (new \DirectoryIterator($directoryPath) as $file) {
			if ($file->isFile() && !\in_array($file->getFilename(), $filesNamesToOmit, false)) {
				try {
					self::remove($file->getPathname());
				} catch (IOExceptionInterface $e) {
					echo '<error>An error occurred while deleting ' . $e->getPath() . '</error>';
				}
				$count++;
			}
		}

		return $count;
	}
}
