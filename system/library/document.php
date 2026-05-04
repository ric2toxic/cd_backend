<?php
class Document {
	private $title;
	private $description;
	private $keywords;
	private $links = array();
	private $styles = array();
	private $scripts = array();
	private $additional_scripts = array();
    private $social_meta_tags = '';

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function addLink($href, $rel) {
		$this->links[$href] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}

	public function getLinks() {
		return $this->links;
	}

	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[$href] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	public function getStyles() {
		return $this->styles;
	}

	public function addScript($script) {
		$this->scripts[md5($script)] = $script;
	}

	public function getScripts() {
		return $this->scripts;
	}

	public function addAdditionalScript($additional_script) {
		$this->additional_scripts[md5($additional_script)] = $additional_script;
	}

	public function getAdditionalScript() {
		return $this->additional_scripts;
	}

	public function getSocialMetaTags() {
        return $this->social_meta_tags;
    }

    public function setSocialMetaTags($meta_html) {
        return $this->social_meta_tags = $meta_html;
    }
}