<h2> 1.1 New Clearpay Installation </h2>
<p> This section outlines the steps to install the Clearpay plugin on your Magento instance for the first time. </p>

<p> Magento can be installed in any folder on the merchant’s server however for the purposes of this document, [MAGENTO] will refer to the root folder where Magento is installed. </p>

<ol>
	<li> The Magento-Clearpay plugin is available as a .zip or tar.gz file from the Clearpay GitHub directory.</li>
	<li> Unzip the file and follow the following instructions to copy the files to the Magento website directory. </li>
	<li> Copy all files in: <br/><em>/app/code/community/</em> <br/> to: <br/> <em>[MAGENTO]/app/code/community</em> </li>
	<li> Copy all files in: <br/><em>/app/design/frontend/base/default/layout</em> <br/> to: <br/> <em>[MAGENTO]/design/frontend/base/default/layout</em> </li>
	<li> Copy all files in: <br/><em>/app/design/frontend/base/default/template</em> <br/> to: <br/> <em>[MAGENTO]/app/design/frontend/base/default/template</em> </li>
	<li> Copy all files in: <br/><em>/design/adminhtml/default/default/template</em> <br/> to: <br/> <em>[MAGENTO] /design/adminhtml/default/default/template</em> </li>
	<li> Copy all files in: <br/><em>/app/etc</em> <br/> to: <br/> <em>[MAGENTO]/app/etc</em> </li>
	<li> Copy all files in: <br/><em>/js</em> <br/> to: <br/> <em>[MAGENTO]/js</em> </li>
	<li> Copy all files in: <br/><em>/skin/frontend/base/default</em> <br/> to: <br/> <em>[MAGENTO]/skin/frontend/base/default</em> </li>
	<li> Login to Magento Admin and navigate to System/Cache Management </li>
	<li> Flush the cache storage by selecting <em>Flush Cache Storage</em> </li>
</ol>

<h2> 1.2	Website Configuration </h2>
<p> Clearpay operates under a list of assumptions based on Magento configurations. To align with these assumptions, the Magento configurations must reflect the below. </p>

<ol>
	<li> <p><strong>Website Currency must be set to GBP</strong></p> Navigate to <em>Magento Admin/System/Configuration/Currency Setup</em> Set the base, display and allowed currency to GBP.</li>
	<li> <p><strong>Postcode must be mandatory</strong></p> Navigate to <em>Magento Admin/System/Configuration/General Deselect</em>. United Kingdom from <em>Postal Code is Optional for the following countries</em>.</li>
</ol>

<h2> 1.3	Clearpay Merchant Setup </h2>
<p> To configure the merchant’s Clearpay Merchant Credentials in Magento Admin complete the below steps. Prerequisite for this section is to obtain an Clearpay Merchant ID and Secret Key from Clearpay. </p>

<ol>
	<li> Navigate to <em>Magento Admin/System/Configuration/Sales/Payment Methods/Clearpay</em> </li>
	<li> Enter the Merchant ID and Merchant Key (provided by Clearpay)  </li>
	<li> Enable Clearpay plugin using the <em>Enabled</em> checkbox. </li>
	<li> Configure the Clearpay API Mode (<em>Sandbox Mode</em> for testing on a staging instance and <em>Production Mode</em> for a live website and legitimate transactions). </li>
	<li> Save the configuration. </li>
	<li> Click the <em>Update Payment Limits</em> button to retrieve the Minimum and Maximum Clearpay Order values.  </li>
</ol>

<h2> 1.4	Clearpay Display Configuration </h2>

<ol>
	<li> Navigate to <em>System/Configuration/Sales/Clearpay</em> </li>
	<li> Enable <em>Debug Mode</em> to log transactions and ensure additional valuable data  </li>
	<li> Configure the display of the Clearpay instalments details on <em>Product Pages</em> (individual product display pages) and <em>Category Pages</em> (the listing of products, which would also include Search Pages). </li>
	<li> Login to Magento Admin and navigate to <em>System/Cache Management</em>. </li>
	<li> Flush the cache storage by selecting <em>Flush Cache Storage</em> </li>
</ol>

<h2> 1.5	Upgrade Of Clearpay Installation </h2>
<p> This section outlines the steps to upgrade the currently installed Magento-Clearpay plugin version.</p>
<p> The process of upgrading the Magento-Clearpay plugin involves the complete removal of Magento-Clearpay plugin files, followed by copying the new files.</p>
<p> [MAGENTO] will refer to the root folder where you have installed your version of Magento. </p>

<ol>
	<li> The Magento-Clearpay plugin is available as a .zip or tar.gz file from the Clearpay GitHub directory. </li>
	<li> Unzip the file and follow the following instructions to copy the files to the Magento website directory. </li>
	<li> Remove all files in: <br/> <em>[MAGENTO]/app/code/community/Clearpay</em></li>
	<li> Copy new files to: <br/> <em>[MAGENTO]/app/code/community/Clearpay</em></li>
	<li> Remove all files in: <br/> <em>[MAGENTO]/app/design/frontend/base/default/layout/clearpay.xml</em></li>
	<li> Copy new files to: <br/> <em>[MAGENTO]/design/frontend/base/default/layout/clearpay.xml</em></li>
	<li> Remove all files in: <br/> <em>[MAGENTO]/app/design/frontend/base/default/template/clearpay</em></li>
	<li> Copy new files to: <br/> <em>[MAGENTO]/app/design/frontend/base/default/template/clearpay</em></li>
	<li> Remove all files in: <br/> <em>[MAGENTO]/design/adminhtml/default/default/template/Clearpay</em></li>
	<li> Copy new files to: <br/> em>[MAGENTO]/design/adminhtml/default/default/template/Clearpay</em></li>
	<li> Remove all files in: <br/> <em>[MAGENTO]/app/etc/modules/Clearpay_Clearpay.xml</em></li>
	<li> Copy new files to: <br/> <em>[MAGENTO]/app/etc/modules/Clearpay_Clearpay.xml</em></li>
	<li> Remove all files in: <br/> <em>[MAGENTO]/js/Clearpay</em></li>
	<li> Copy new files to: <br/> <em>[MAGENTO]/js/Clearpay</em></li>
	<li> Remove all files in: <br/> <em>[MAGENTO]/skin/frontend/base/default/clearpay</em></li>
	<li> Copy new files to: <br/> <em>[MAGENTO]/skin/frontend/base/default/clearpay</em></li>
	<li> Login to Magento Admin and navigate to <em>System/Cache Management</em> </li>
	<li> Flush the cache storage by selecting <em>Flush Cache Storage</em> </li>
</ol>