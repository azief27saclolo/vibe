<?php
session_start();
include 'Inventory.php';
$inventory = new Inventory();
if(!empty($_GET['action']) && $_GET['action'] == 'logout') {
	session_unset();
	session_destroy();
	header("Location:index.php");
}
if(!empty($_POST['action']) && $_POST['action'] == 'getInventoryDetails') {
	$inventory->getInventoryDetails();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getRevenueData') {
	$inventory->getRevenueData();
}
// Customer management
if(!empty($_POST['action']) && $_POST['action'] == 'customerList') {
	$inventory->getCustomerList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'customerAdd'){
	$inventory->saveCustomer();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getCustomer'){
	$inventory->getCustomer();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'customerUpdate'){
	$inventory->updateCustomer();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'customerDelete'){
	$inventory->deleteCustomer();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getCustomerList') {
    $inventory->getCustomerListDropdown();
}
// Category management
if(!empty($_POST['action']) && $_POST['action'] == 'categoryList') {
	$inventory->getCategoryList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'categoryAdd'){
	$inventory->saveCategory();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getCategory'){
	$inventory->getCategory();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateCategory'){
	$inventory->updateCategory();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteCategory'){
	$inventory->deleteCategory();
}
// Brand management
if(!empty($_POST['action']) && $_POST['action'] == 'listBrand') {
	$inventory->getBrandList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addBrand'){
	$inventory->saveBrand();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getBrand'){
	$inventory->getBrand();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateBrand'){
	$inventory->updateBrand();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteBrand'){
	$inventory->deleteBrand();
}
// Product management
if(!empty($_POST['action']) && $_POST['action'] == 'listProduct') {
	$inventory->getProductList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getCategoryBrand') {
	echo $inventory->getCategoryBrand($_POST['categoryid']);
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addProduct') {
	$inventory->addProduct();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getProductDetails') {
	$inventory->getProductDetails();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateProduct'){
	$inventory->updateProduct();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteProduct'){
	$inventory->deleteProduct();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'viewProduct'){
	$inventory->viewProductDetails();
}
// manage supplier
if(!empty($_POST['action']) && $_POST['action'] == 'supplierList') {
	$inventory->getSupplierList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addSupplier'){
	$inventory->addSupplier();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getSupplier'){
	$inventory->getSupplier();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateSupplier'){
	$inventory->updateSupplier();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteSupplier'){
	$inventory->deleteSupplier();
}
// manage purchase
if(!empty($_POST['action']) && $_POST['action'] == 'listPurchase') {
	$inventory->listPurchase();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addPurchase'){
	$inventory->addPurchase();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getPurchaseDetails'){
	$inventory->getPurchaseDetails();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updatePurchase'){
	$inventory->updatePurchase();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deletePurchase'){
	$inventory->deletePurchase();
}
// manage purchase
if(!empty($_POST['action']) && $_POST['action'] == 'listOrder') {
	$inventory->listOrders();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addOrder'){
	$inventory->addOrder();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getOrderDetails'){
	$inventory->getOrderDetails();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateOrder'){
	$inventory->updateOrder();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteOrder'){
	$inventory->deleteOrder();
}

// manage replace
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addReplaced'){
	$inventory->addReplaced();
}

if(!empty($_POST['action']) && $_POST['action'] == 'listReplaced') {
	$inventory->listReplaced();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteReplaced'){
	$inventory->deleteReplaced();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getReplacedDetails'){
	$inventory->getReplacedDetails();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateReplaced'){
	$inventory->updateReplaced();
}

//Services
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addServices'){
	$inventory->addServices();
}

if(!empty($_POST['action']) && $_POST['action'] == 'listServices') {
	$inventory->listServices();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteServices'){
	$inventory->deleteServices();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateServices'){
	$inventory->updateServices();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getServicesDetails'){
	$inventory->getServicesDetails();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getServiceList') {
    $inventory->getServiceListDropdown();
}

//Service Availed

if(!empty($_POST['action']) && $_POST['action'] == 'service_availedList') {
	$inventory->service_availedList();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addServiceAvailed'){
    $inventory->addServiceAvailed();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getServiceAvailedDetails'){
    $inventory->getServiceAvailedDetails();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateServiceAvailed'){
    $inventory->updateServiceAvailed();
}

if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteServiceAvailed'){
    $inventory->deleteServiceAvailed();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getAvailableParts') {
    $inventory->getAvailableParts();
}
?>
