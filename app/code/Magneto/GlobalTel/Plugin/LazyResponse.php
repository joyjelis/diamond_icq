<?php

namespace Magneto\GlobalTel\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\View\Asset\Repository;
use Magneto\GlobalTel\Helper\Data;

class LazyResponse {

	protected $request;

	protected $helper;

	protected $content;

	protected $isJson;

	protected $exclude = [];

	protected $scripts = [];

	protected $skipimage = false;

	protected $placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';

	public function __construct(
		RequestInterface $request,
		Repository $assetRepo,
		Data $helper,
		array $data = []
	) {
		$this->request = $request;
		$this->_assetRepo = $assetRepo;
		$this->helper = $helper;
		// $this->placeholder = $this->_assetRepo->getUrl("Magneto_GlobalTel::images/loader.gif");
	}

	/**
	 * @param Http $subject
	 * @return void
	 */
	public function beforeSendResponse(Http $response) {
		// return;

		if (!$this->helper->getConfigModule('lazy_load')) {
			return;
		}

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$logger = $objectManager->create(\Magneto\Logger\Logger\Handler::class);
		// $logger->log(print_r($response->getBody(), true));

		if ($this->request->isXmlHttpRequest()) {
			$shopbyAjax = $this->helper->getConfigModule('amasty_ajax');

			if ($shopbyAjax) {
				if ($this->request->getParam('shopbyAjax')) {
					return;
				}
			}

			$lazyAjax = $this->helper->getConfigModule('lazy_ajax');

			if ($this->request->getControllerModule() == "Gemfind_Ringbuilder") {
				$this->skipimage = true;
			}

			// $logger->log("\n");
			// $logger->log(print_r($this->request->getControllerModule(), true));

			if (!$lazyAjax) {
				return;
			}

			$contentType = $response->getHeader('Content-Type');
			if ($contentType && $contentType->getMediaType() == 'application/json') {
				$this->isJson = true;
			}
		}

		$body = $response->getBody();
		$bodyClass = 'loading_img';
		$loadingBody = $this->helper->getConfigModule('loading_body');
		if ($loadingBody) {
			$bodyClass .= ' loading_body';
		}

		$exclude = $this->helper->getConfigModule('exclude_img');

		if ($exclude) {
			$exclude = str_replace(' ', '', $exclude);
			$this->exclude = explode(',', $exclude);
		}

		$placeholder = $this->helper->getConfigModule('placeholder');
		$regex_block = "";

		if ($this->skipimage != true) {
			$body = $this->addLazyload($body, $placeholder, $regex_block);
		}

		$body = $this->addLazyloadPlaceholderForVideo($body);
		$body_includes = $this->helper->getConfigModule('body_includes');
		if ($body_includes) {
			$body = $this->addToBottomBody($body, $body_includes);
		}

		$response->setBody($body);

	}

	public function addLazyload($content, $placeholder = false, $start = 0) {
		if ($start && !is_numeric($start)) {
			$start = strpos($content, $start);
		}

		$html = '';

		if ($start) {
			$page = str_split($content, $start);
			$isFirst = true;

			foreach ($page as $key => $pg) {
				if (!$key) {
					$html .= $pg;
				} else {
					if ($placeholder) {
						$pg = $this->addLazyloadPlaceholder($pg);
					}

					$html .= $this->addLazyloadAll($pg);
				}
			}
		} else {
			if ($placeholder) {
				$content = $this->addLazyloadPlaceholder($content);
			}

			$html .= $this->addLazyloadAll($content);
		}

		return $html;
	}

	public function addLazyloadPlaceholder($content) {
		if ($this->skipimage == true) {
			return $content;
		}

		$placeholder = $this->placeholder;

		$content = preg_replace_callback_array(
			[
				'/<img([^>]+?)width=[\'"]?([^\'"\s>]+)[\'"]([^>]+?)height=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/' => function ($match) use ($placeholder) {
					return $this->addLazyloadImage($match[0], $placeholder);
				},
				'/<img([^>]+?)height=[\'"]?([^\'"\s>]+)[\'"]([^>]+?)width=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/' => function ($match) use ($placeholder) {
					return $this->addLazyloadImage($match[0], $placeholder);
				},
			],
			$content
		);

		return $content;
	}

	public function addLazyloadPlaceholderForVideo($content) {
		$content = preg_replace_callback_array(
			[
				'/<video([^>]+?)width=[\'"]?([^\'"\s>]+)[\'"]([^>]+?)height=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/' => function ($match) {
					return $this->addLazyloadVideo($match[0]);
				},
				'/<video([^>]+?)height=[\'"]?([^\'"\s>]+)[\'"]([^>]+?)width=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/' => function ($match) {
					return $this->addLazyloadVideo($match[0]);
				},
			],
			$content
		);

		return $content;
	}

	public function isExclude($class) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $logger = $objectManager->create(\Magneto\Logger\Logger\Handler::class);
		// $logger->log("\n");
		// $logger->log($class);
		// $logger->log("\n");
		if (is_string($class)) {
			$class = explode(' ', $class);
		}

		$excludeExist = array_intersect($this->exclude, $class);
		return !empty($excludeExist);
	}

	public function addLazyloadVideo($content) {
		if ($this->isJson) {
			return $this->addLazyloadVideoJson($content);
		}

		return preg_replace_callback(
			'/<video\s*.*?(?:class="(.*?)")?([^>]*)>/',
			function ($match) {

				if (stripos($match[0], ' data-src="') !== false) {
					return $match[0];
				}

				if (stripos($match[0], ' class="') !== false) {
					if ($this->isExclude($match[1])) {
						return $match[0];
					}

					$lazy = str_replace(' class="', ' class="lazy ', $match[0]);
				} else {
					$lazy = str_replace('<video ', '<video class="lazy" ', $match[0]);
				}

				return str_replace(' src="', ' data-poster="' . $this->placeholder . '" data-src="', $lazy);
			},
			$content
		);
	}

	public function addLazyloadImage($content, $placeholder) {
		if ($this->skipimage == true) {
			return $content;
		}

		if ($this->isJson) {
			return $this->addLazyloadImageJson($content, $placeholder);
		}

		return preg_replace_callback(
			'/<img\s*.*?(?:class="(.*?)")?([^>]*)>/',
			function ($match) use ($placeholder) {

				if (stripos($match[0], ' data-src="') !== false) {
					return $match[0];
				}

				if (stripos($match[0], ' class="') !== false) {
					if ($this->isExclude($match[1])) {
						return $match[0];
					}

					$lazy = str_replace(' class="', ' class="lazy ', $match[0]);
				} else {
					$lazy = str_replace('<img ', '<img class="lazy" ', $match[0]);
				}

				/* break if exist data-src */
				// if(strpos($lazy, ' data-src="')) return $lazy;

				return str_replace(' src="', ' src="' . $placeholder . '" data-src="', $lazy);
			},
			$content
		);
	}

	public function addLazyloadVideoJson($content) {
		$placeholder = addslashes($this->placeholder);
		return preg_replace_callback(
			'/<video\s*.*?(?:class=\\\"(.*?)\\\")?([^>]*)>/',
			function ($match) use ($placeholder) {

				// if (stripos($match[0], ' data-src=\"') !== false) {
				// 	return $match[0];
				// }

				if (stripos($match[0], ' class="') !== false) {
					if ($this->isExclude($match[1])) {
						return $match[0];
					}
					$lazy = str_replace(' class=\"', ' class=\"lazy ', $match[0]);
				} else {
					$lazy = str_replace('<video ', '<video class=\"lazy\" ', $match[0]);
				}

				/* break if exist data-src */
				// if(strpos($lazy, ' data-src=\"')) return $lazy;

				return str_replace(' src=\"', ' data-poster=\"' . $placeholder . '\" data-src=\"', $lazy);
			},
			$content
		);
	}

	public function addLazyloadImageJson($content, $placeholder) {

		$placeholder = addslashes($placeholder);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$logger = $objectManager->create(\Magneto\Logger\Logger\Handler::class);
		// $logger->log("addLazyloadImageJson\n");
		return preg_replace_callback(
			'/<img\s*.*?(?:class=\\\"(.*?)\\\")?([^>]*)>/',
			function ($match) use ($placeholder, $logger) {
				// $logger->log(print_r($match, true));
				// $logger->log("\n");
				if (stripos($match[0], ' data-src=\"') !== false) {
					return $match[0];
				}

				if (stripos($match[0], ' class="') !== false) {
					if ($this->isExclude($match[1])) {
						return $match[0];
					}

					$lazy = str_replace(' class=\"', ' class=\"lazy ', $match[0]);
				} else {
					$lazy = str_replace('<img ', '<img class=\"lazy\" ', $match[0]);
				}

				/* break if exist data-src */
				// if(strpos($lazy, ' data-src=\"')) return $lazy;

				return str_replace(' src=\"', ' src=\"' . $placeholder . '\" data-src=\"', $lazy);
			},
			$content
		);
	}

	public function addLazyloadAll($content) {
		$placeholder = $this->placeholder;
		$content = $this->addLazyloadImage($content, $placeholder);
		return $content;
	}

	public function addToTopBody($content, $insert) {
		return $content = preg_replace_callback(
			'/<body([\s\S]*?)?([^>]*)>/',
			function ($match) use ($insert) {
				return $match[0] . $insert;
			},
			$content
		);
	}

	public function addToBottomBody($content, $insert) {
		$content = str_ireplace('</body>', $insert . '</body>', $content);
		return $content;
	}
}