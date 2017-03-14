// package list
$package_list_sql='';
$package_list_sql+='SELECT p.packageid, p.packagenumber, p.createdate, p.total_tracking, p.shippingno, p.amount, p.statusid, p.adduser, p.sentemail, ';
$package_list_sql+='c.customer_firstname, c.customer_lastname, p.shippingname ';
$package_list_sql+='FROM package p LEFT JOIN customer c ON p.customerid = c.customer_id ';
$package_list_sql+='LEFT JOIN website_transport s ON p.shippingid = s.transport_id ';
$package_list_sql+='ORDER BY p.createdate DESC';

// detail package
$package_detail_sql='';
$package_detail_sql+='SELECT p.packagenumber, p.createdate, p.customerid, p.shippingno, ';
$package_detail_sql+='d.packageorder, o.order_number, c.m3, c.weight, c.rate, c.total ';
$package_detail_sql+='FROM package_detail d INNER JOIN package p ON d.packageid = p.packageid ';
$package_detail_sql+='LEFT JOIN customer_order_product_tracking c ON d.order_product_id = c.order_product_id AND d.order_id = c.order_id AND d.order_product_tracking_id = c.order_product_tracking_id ';
$package_detail_sql+='LEFT JOIN customer_order o ON d.order_id = c.order_id ';
$package_detail_sql+='WHERE d.packageid = ? ';
$package_detail_sql+='ORDER BY d.packageorder DESC';

$new_package_sql='insert into package (packagenumber, customerid, createddate, total_tracking, shippingid, shippingno, amount, statusid, adduser, adddate, sentemail) values (?, ?, ?, 0, ?, ?, 0, 0, ?, NOW(), 0)';

