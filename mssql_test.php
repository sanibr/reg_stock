<?php
$myServer = "sql5066.site4now.net";
$myUser = "DB_A67E76_InfonetTestTextile_admin";
$myPass = "GrandBake_230130";
$myDB = "DB_A67E76_InfonetTestTextile";

$conn = mssql_connect($myServer,$myUser,$myPass,$myDB);
print($conn);
//$db_selected = mssql_select_db($myDB, $conn);
if ($conn) 
{



// $sql = "SELECT sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date,sum(sm.Total_Gross_Amount) as Total_Gross_Amount,sum(sm.Total_Discount_Amount) as Total_Discount_Amount,sum(sm.Total_Net_Amount) as Total_Net_Amount,sum(sm.Total_Tax_Amount) as Total_Tax_Amount,sum(sm.Total_Amount) as Total_Amount,sum(sm.Discount_Amount) as Discount_Amount,sum(sm.Grand_Total) as Grand_Total 
// FROM sales_master sm 
// left join customer sc on sm.Customer_Id=sc.Customer_Id 
// left join company c on sm.Company_Id=c.Company_Id 
// where 1=1 and sm.Invoice_Date >='2020-01-01' and sm.Invoice_Date <='2020-12-25' 
// group by sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date order by sm.Sales_Master_Id desc";

// $sql = "select * from (
// SELECT ROW_NUMBER() OVER(ORDER BY Sales_Master_Id desc) as Rownumber,sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date,sum(sm.Total_Gross_Amount) as Total_Gross_Amount,sum(sm.Total_Discount_Amount) as Total_Discount_Amount,sum(sm.Total_Net_Amount) as Total_Net_Amount,sum(sm.Total_Tax_Amount) as Total_Tax_Amount,sum(sm.Total_Amount) as Total_Amount,sum(sm.Discount_Amount) as Discount_Amount,sum(sm.Grand_Total) as Grand_Total 
// FROM sales_master sm 
// left join customer sc on sm.Customer_Id=sc.Customer_Id 
// left join company c on sm.Company_Id=c.Company_Id 
// where 1=1 and sm.Invoice_Date >='2020-01-01' and sm.Invoice_Date <='2020-12-25' 
// group by sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date) as f
// where f.Rownumber between 0 and 10 order by Sales_Master_Id desc";


// $sql = "SELECT sm.Sales_Master_Id,sm.Serial_No,FORMAT(sm.Invoice_Date,'dd/MM/yyyy') AS Invoice_Date,sm.Invoice_No,sm.Customer_Name,sm.IsCash,CASE WHEN sm.IsCash = 0 THEN 'Cash' WHEN sm.IsCash = 1 THEN 'Card' WHEN sm.IsCash = 2 THEN 'Credit' WHEN sm.IsCash = 3 THEN 'Multi' ELSE 'Cash' END as paymentmode,sm.Total_Gross_Amount,sm.Total_Discount_Amount,sm.Total_Net_Amount,sm.Total_Tax_Amount,sm.Total_Amount,sm.Discount_Amount,sm.Grand_Total 
// FROM sales_master sm 
// left join customer sc on sm.Customer_Id=sc.Customer_Id 
// where 1=1 and sm.Invoice_Date >='2020-01-24' and sm.Invoice_Date <='2020-12-25' order by sm.Sales_Master_Id desc";


// $sql = "SELECT month(sm.Invoice_Date) month,sum(sm.Grand_Total) total,sm.Company_Id,c.Company_Name,c.Color 
//         FROM  sales_master sm
// 		left join company c on sm.Company_Id=c.Company_ID
// 	    where  sm.Invoice_Date between '2020-12-25' and '2020-12-01'
// 		group by sm.Company_Id,month(sm.Invoice_Date)";

// $sql = "SELECT month(sm.Invoice_Date) month,sum(sm.Grand_Total) total,sm.Company_Id,c.Company_Name
//         FROM  sales_master sm
// 		left join company c on sm.Company_Id=c.Company_ID
// 	    where  sm.Invoice_Date between '2020-12-01' and '2020-12-25'
// 		group by month(sm.Invoice_Date),sm.Company_Id,c.Company_Name";


// $sql = "select * from (SELECT ROW_NUMBER() OVER(ORDER BY Sales_Master_Id desc) as Rownumber, sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date,sum(sm.Total_Gross_Amount) as Total_Gross_Amount,sum(sm.Total_Discount_Amount) as Total_Discount_Amount,sum(sm.Total_Net_Amount) as Total_Net_Amount,sum(sm.Total_Tax_Amount) as Total_Tax_Amount,sum(sm.Total_Amount) as Total_Amount,sum(sm.Discount_Amount) as Discount_Amount,sum(sm.Grand_Total) as Grand_Total
// FROM sales_master sm 
// left join customer sc on sm.Customer_Id=sc.Customer_Id left join company c on sm.Company_Id=c.Company_Id 
// where 1=1 and sm.Invoice_Date >='2020-01-01' and sm.Invoice_Date <='2020-12-24' 
// group by sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date) as f where f.Rownumber between 10 and 10 
// order by Sales_Master_Id desc";


$sql = "SELECT sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date,sum(sm.Total_Gross_Amount) as Total_Gross_Amount,sum(sm.Total_Discount_Amount) as Total_Discount_Amount,sum(sm.Total_Net_Amount) as Total_Net_Amount,sum(sm.Total_Tax_Amount) as Total_Tax_Amount,sum(sm.Total_Amount) as Total_Amount,sum(sm.Discount_Amount) as Discount_Amount,sum(sm.Grand_Total) as Grand_Total
FROM sales_master sm 
left join customer sc on sm.Customer_Id=sc.Customer_Id left join company c on sm.Company_Id=c.Company_Id 
where 1=1 and sm.Invoice_Date >='2020-01-01' and sm.Invoice_Date <='2020-12-24' 
group by sm.Sales_Master_Id,sm.Company_Id,c.Company_Name,sm.Invoice_Date 
order by Sales_Master_Id desc 
OFFSET 10 ROWS 
FETCH NEXT 10 ROWS ONLY";
		
		
		



$result = mssql_query($sql,$conn);
//$result = $this->connection->mssql_query($query);

    if($result === false) {
        die(print_r(mssql_errors(), true));
    }else {
        echo mssql_get_last_message();
         while ($row = mssql_fetch_array($result)) {
              var_dump($row);
              
         }
    }
} 
?>