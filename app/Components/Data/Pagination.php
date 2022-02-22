<?php

namespace App\Components\Data;

use Illuminate\Database\Eloquent\Builder;

class Pagination
{
    /**
     * Current Page
     */
    protected $current_page = 1;

    /**
     * Page param
     */
    protected $page_param = 'page';

    /**
     * Page Size
     */
    protected $page_size = 10;

    /**
     * Page size param
     */
    protected $page_size_param = 'size';

    /**
     * Max Button Count
     * [First][Prev][1][2][3][Next][Last]
     */
    protected $max_button_count = 3;

    /**
     * Instance class of Illuminate\Database\Eloquent\Builder
     */
    protected $model;

    /**
     * Total row from table
     */
    protected $total_row = 0;

    /**
     * Data table
     */
    protected $data_table = [];

    /**
     * Data pagination
     */
    protected $data_pagination = [];

    /**
     * Button of pagination
     */
    protected $total_button = 0;

    /**
     * Paging Label : [First], [Prev], [Next], [Last]
     */
    protected $label_first = 'First';
    protected $label_previous = 'Prev';
    protected $label_next = 'Next';
    protected $label_last = 'Last';


    public function __construct(Builder $model, $config = [])
    {
        $this->model = $model;

        $this->page_param = array_key_exists('pageParam', $config) ? $config['pageParam'] : $this->page_param;
        $this->page_size_param = array_key_exists('pageSizeParam', $config) ? $config['pageParam'] : $this->page_size_param;

        $this->current_page = (int)(array_key_exists('pageParam', $config) ? request()->get($config['pageParam']) : request()->get($this->page_param, $this->current_page));
        $this->page_size = (int)(array_key_exists('pageSizeParam', $config) ? request()->get($config['pageSizeParam']) : request()->get($this->page_size_param, $this->page_size));

        $this->max_button_count = (int)(array_key_exists('maxButtonCount', $config) ? $config['maxButtonCount'] : $this->max_button_count);

        $this->init();
    }

    protected function init()
    {
        $currentPage = $this->getCurrentPage();
        $pageCount = $this->getPageSize();
        $buttons = [];

        // first page
        $buttons[] = [
            'label' => 'First',
            'page' => 0,
            'class' => null,
            'disabled' => $currentPage <= 0,
            'active' => false
        ];

        // prev page
        if (($page = $currentPage - 1) < 0) {
            $page = 0;
        }
        $buttons[] = [
            'label' => 'Prev',
            'page' => $page,
            'class' => null,
            'disabled' => $currentPage <= 0,
            'active' => false
        ];

        // internal page
        list($beginPage, $endPage) = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = [
                'label' => $i + 1,
                'page' => $i,
                'class' => null,
                'disabled' => $i == $currentPage,
                'active' => $i == $currentPage
            ];
        }

        // next page
        if (($page = $currentPage + 1) >= $pageCount - 1) {
            $page = $pageCount - 1;
        }
        $buttons[] = [
            'label' => 'Next',
            'page' => $page,
            'class' => null,
            'disabled' => $currentPage >= $pageCount - 1,
            'active' => false
        ];

        // last page
        $buttons[] = [
            'label' => 'Last',
            'page' => $pageCount - 1,
            'class' => null,
            'disabled' => $currentPage >= $pageCount - 1,
            'active' => false
        ];

        print('<pre>'.print_r($buttons,true).'</pre>');exit;
    }

    protected function getPageRange()
    {
        $currentPage = $this->getCurrentPage();
        $pageCount = $this->getPageSize();

        $beginPage = max(0, $currentPage - (int) ($this->getMaxButtonCount() / 2));
        if (($endPage = $beginPage + $this->getMaxButtonCount() - 1) >= $pageCount) {
            $endPage = $pageCount - 1;
            $beginPage = max(0, $endPage - $this->getMaxButtonCount() + 1);
        }

        return [$beginPage, $endPage];
    }

    protected function init_v2()
    {
        $this->total_row = $this->model->count();
        $offset = ($this->current_page - 1) * $this->page_size;
        $this->data_table = $this->model->limit($this->page_size)->offset($offset);
        $this->total_button = ceil($this->total_row / $this->page_size);

        // page_size = 10
        // current_page = 1
        // total_row = 342
        // total_button = 35
        // max_button = 3

        // 3 / 2 = 1.5 => Ceil 2
        $tengah = ceil($this->max_button_count / 2);
        // 3 - 2 = 1
        $batas_sisa_tombol = $this->max_button_count - $tengah;

        // Kondisi "current_page" = 1
        // 35 - 1 = 34
        $sisa_awal = $this->total_button - $this->current_page;
        // 35 - 34 = 1
        $sisa_akhir = $this->total_button - $sisa_awal;
        // Kondisi "current_page" = 34
        // 35 - 34 = 1
        // $sisa_awal = $this->total_button - $this->current_page;
        // 35 - 1 = 34
        // $sisa_akhir = $this->total_button - $sisa_awal;

        // Deteksi posisi aktif tombol yang bergeser
        $tombol_bergeser = ($sisa_awal<=$batas_sisa_tombol)||($sisa_akhir<=$batas_sisa_tombol) ? false : true;

        $links = [];
        if($tombol_bergeser) {

        } else {

        }

        $this->data_pagination = [
            'current_page' => $this->getCurrentPage(),
            'page_size' => $this->getPageSize(),
            'total_row' => $this->getTotalRow(),
            'total_button' => $this->getTotalButton(),
            'links' => $tombol_bergeser
        ];
    }

    protected function init_v1()
    {
        $this->total_row = $this->model->count();
        $offset = ($this->current_page - 1) * $this->page_size;
        $this->data_table = $this->model->limit($this->page_size)->offset($offset);
        $this->total_button = ceil($this->total_row / $this->page_size);

        // ===========================================================================
        $links = [];
        $sisa_bagi = $this->current_page % $this->max_button_count;
        $is_toggle = $sisa_bagi==0 ? true : false;
        // print('<pre>'.print_r($sisa_bagi).'</pre>');exit;

        $sisa_button = $this->total_button;
        $start = $is_toggle ? ($this->current_page-1) : ($this->current_page-$sisa_bagi);

        $current = 0;
        while($start < $this->total_button) {
            $current = $start + 1;
            //$sisa_button = $sisa_button - 1;//--

            // $is_toggle = ($current % $this->max_button_count)==0 ? true : false;
            // $status = $is_toggle==true ? 'inactive' : ($this->current_page==$current ? 'active' : 'inactive');
            $links[] = [
                'label' => $current,
                'status' => $this->current_page==$current ? 'active' : 'inactive',
                'query' => "?{$this->page_param}={$current}&{$this->page_size_param}={$this->page_size}"
            ];
            if(count($links) == $this->max_button_count) {
                break;
            }
        }
        // ===========================================================================
        $is_disabled = $this->current_page <= 1 ? true : false;
        $page_value = $this->current_page - 1;
        array_unshift($links, [
            'label' => $this->label_first,
            'status' => $is_disabled ? 'disabled' : 'visibility',
            'query' => $is_disabled ? null : "?{$this->page_param}=1&{$this->page_size_param}={$this->page_size}"
        ], [
            'label' => $this->label_previous,
            'status' => $is_disabled ? 'disabled' : 'visibility',
            'query' => $is_disabled ? null : "?{$this->page_param}={$page_value}&{$this->page_size_param}={$this->page_size}"
        ]);
        // ===========================================================================
        $is_disabled = $this->current_page >= $this->total_button ? true : false;
        $page_value = $this->current_page + 1;
        array_push($links, [
            'label' => $this->label_next,
            'status' => $is_disabled ? 'disabled' : 'visibility',
            'query' => $is_disabled ? null : "?{$this->page_param}={$page_value}&{$this->page_size_param}={$this->page_size}"
        ], [
            'label' => $this->label_last,
            'status' => $is_disabled ? 'disabled' : 'visibility',
            'query' => $is_disabled ? null : "?{$this->page_param}={$this->total_button}&{$this->page_size_param}={$this->page_size}"
        ]);
        // ===========================================================================

        $this->data_pagination = [
            'current_page' => $this->getCurrentPage(),
            'page_size' => $this->getPageSize(),
            'total_row' => $this->getTotalRow(),
            'total_button' => $this->getTotalButton(),
            'links' => $links
        ];
    }

    public function getData()
    {
        return $this->data_table->get();
    }

    public function getPagination()
    {
        return $this->data_pagination;
    }

    public function getTotalRow()
    {
        return $this->total_row;
    }

    public function getTotalButton()
    {
        return $this->total_button;
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function getPageSize()
    {
        return $this->page_size;
    }

    public function getMaxButtonCount()
    {
        return $this->max_button_count;
    }
}

?>
