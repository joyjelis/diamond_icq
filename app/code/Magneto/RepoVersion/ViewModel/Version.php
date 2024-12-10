<?php

namespace Magneto\RepoVersion\ViewModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Version implements ArgumentInterface {

	/**
	 * @var Filesystem
	 */
	private $_filesystem;

	/**
	 * Constructor
	 *
	 * @param Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		$this->_filesystem = $filesystem;
	}

	public function getDeployVersion() {
		$rootPath = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT);

		$content = NULL;
		if ($rootPath->isFile('package.json')) {
			$content = json_decode($rootPath->readFile('package.json'));
		}

		return isset($content->version) ? "v" . $content->version : "";
	}
}
