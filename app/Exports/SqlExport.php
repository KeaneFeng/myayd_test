<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SqlExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $headings;
    protected $columnWidth = [];//设置列宽       key：列  value:宽
    protected $rowHeight = [];  //设置行高       key：行  value:高
    protected $mergeCells = []; //合并单元格    value:A1:K8
    protected $font = [];       //设置字体       key：A1:K8  value:Arial
    protected $fontSize = [];       //设置字体大小       key：A1:K8  value:11
    protected $bold = [];       //设置粗体       key：A1:K8  value:true
    protected $background = []; //设置背景颜色    key：A1:K8  value:#F0F0F0F
    protected $vertical = [];   //设置定位       key：A1:K8  value:center
    protected $sheetName; //sheet title
    protected $borders = []; //设置边框颜色  key：A1:K8  value:#000000
    //设置页面属性时如果无效   更改excel格式尝试即可
    //构造函数传值
    public function __construct($data, $headings,$sheetName)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->sheetName = $sheetName;
        $this->createData();
    }

    public function headings(): array
    {
        return $this->headings;
    }

    //数组转集合
    public function collection()
    {
        return new Collection($this->data);
    }
    //业务代码
    public function createData()
    {
        $this->data = collect($this->data)->toArray();
    }

    /**
     * @return array
     * [
     *    'B' => 40,
     *    'C' => 60
     * ]
     */
    public function setColumnWidth (array $columnwidth)
    {
        $this->columnWidth = array_change_key_case($columnwidth, CASE_UPPER);
    }

    /**
     * @return array
     * [
     *    1 => 40,
     *    2 => 60
     * ]
     */
    public function setRowHeight (array $rowHeight)
    {
        $this->rowHeight = $rowHeight;
    }

    /**
     * @return array
     * [
     *    A1:K7 => '宋体'
     * ]
     */
    public function setFont (array $font)
    {
        $this->font = array_change_key_case($font, CASE_UPPER);
    }

    /**
     * @return array
     * @2020/3/22 10:33
     * [
     *    A1:K7 => true
     * ]
     */
    public function setBold (array $bold)
    {
        $this->bold = array_change_key_case($bold, CASE_UPPER);
    }

    /**
     * @return array
     * @2020/3/22 10:33
     * [
     *    A1:K7 => F0FF0F
     * ]
     */
    public function setBackground (array $background)
    {
        $this->background = array_change_key_case($background, CASE_UPPER);
    }
    /**
     * @return array
     * [
     *    A1:K7
     * ]
     */
    public function setMergeCells (array $mergeCells)
    {
        $this->mergeCells = array_change_key_case($mergeCells, CASE_UPPER);
    }
    /**
     * @return array
     * [
     *    A1:K7 => 14
     * ]
     */
    public function setFontSize (array $fontSize)
    {
        $this->fontSize = array_change_key_case($fontSize, CASE_UPPER);
    }
    /**
     * @return array
     * [
     *    A1:K7 => #000000
     * ]
     */
    public function setBorders (array $borders)
    {
        $this->borders = array_change_key_case($borders, CASE_UPPER);
    }
}
