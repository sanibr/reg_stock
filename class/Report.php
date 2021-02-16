<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
include_once 'DbConfig.php';

class Report extends DbConfig
{
    protected $dompdf;

    public function __construct()
	{
        parent::__construct();
        $this->dompdf = new Dompdf();		
    }
    
    public function getReport($data, $title, $is_html = 0){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        if($is_html && !is_array($data)){
            $html = '<h2 style="text-align:center;">'.$title.'</h2><br>';
            $html .= $data;
        }else{
            if ($data){
                $html = '<h2 style="text-align:center;">'.$title.'</h2><br>';
                $html .= '<table class="StandardTable"  cellpadding="0" cellspacing="0" border="1" style="border:solid 1px; border-color:black;font-size: 18px;"><tbody>';
                $thead = array_keys($data[0]);
                $html .= '<tr><td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>S.NO</b></td>';
                foreach($thead as $thd){
                    $html .= '<td class="tbl_head" style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$thd.'</b></td>';
                }
                $html .= '</tr>';
                
                foreach ($data as $k => $value) {
                   
                    if(!in_array($k,['sum_gross_amount','sum_total_discount_amount','sum_net_amount','sum_tax_amount','sum_total_amount','sum_discount_amount','sum_grand_total'],true)){
                        
                    $html .= '<tr><td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black">'.($k+1).'</td>';
                    foreach ($value as $key => $val) {                       
                            $html .= '<td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black">'.$val.'</td>';                         
                        }
                    $html .= '</tr>'; 
                 } 
                                 
                }
                $html .=  '<tr>
                                <td colspan="5"></td>
                                <td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$data['sum_gross_amount'].'</b></td>
                                <td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$data['sum_total_discount_amount'].'</b></td>
                                <td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$data['sum_net_amount'].'</b></td>
                                <td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$data['sum_tax_amount'].'</b></td>
                                <td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$data['sum_total_amount'].'</b></td>
                                <td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$data['sum_discount_amount'].'</b></td>
                                <td style="height:25px; padding:5px 5px;border:solid 1px; border-color:black"><b>'.$data['sum_grand_total'].'</b></td></tr>'; 
                $html .= '</tbody></table>';
            }
        }
        

        

      $this->dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $this->dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $this->dompdf->render();

        // Output the generated PDF to Browser
        $this->dompdf->stream(''.$title.'.pdf',array("afichero" => 1, "compress" => 0));
    

    }
}
?>
