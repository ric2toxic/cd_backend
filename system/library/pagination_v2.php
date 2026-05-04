<?php
class PaginationV2 {
	public $page = 'FIRST';
    public $limit = 15;
    public $total = 0;
	public $num_links = 8;
    public $url = '';
    public $next = '';
    public $prev = '';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';

	public function render() {
        $this->text_next = "Next >";
        $this->text_first = "First";
        $this->text_prev = "< Previous";

		$page = $this->page;

		if (!(int)$this->limit) {
			$limit = 15;
		} else {
			$limit = $this->limit;
		}

		$this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

        $right_output = '<div class="col-sm-6 text-right"><ul class="pagination">';
        $left_output = '<div class="col-sm-6 text-left"><ul class="pagination">';
        
        if ($page == 'FIRST') {
            if ($this->total == $limit) {
                $right_output .= '<li><a href="' . str_replace('{page}', $this->next, $this->url) . '">' . $this->text_next . '</a></li>';
                //$right_output .= '<li><a href="' . str_replace('{page}', 'LAST', $this->url) . '">' . $this->text_last . '</a></li>';
            }
        } else {
            $left_output .= '<li><a href="' . str_replace('{page}', 'FIRST', $this->url) . '">' . $this->text_first . '</a></li>';
            $left_output .= '<li><span onclick="window.history.back()" style="cursor: pointer;">' . $this->text_prev . '</span></li>';
            if ($this->total == $limit) {
                $right_output .= '<li><a href="' . str_replace('{page}', $this->next, $this->url) . '">' . $this->text_next . '</a></li>';
                //$right_output .= '<li><a href="' . str_replace('{page}', 'LAST', $this->url) . '">' . $this->text_last . '</a></li>';
            }
        }
	
        $left_output .= '</ul></div>';
        $right_output .= '</ul></div>';
        
        $output = '<div>' . $left_output . $right_output . '</div>';

		return $output;
	}
}