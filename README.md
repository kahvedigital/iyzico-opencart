# iyzico-opencart

iyzico opencart is the simple and lightweight implementation of [iyzico.com](https://www.iyzico.com) payment service for Opencart. It's licensed under LGPL v3.0 license, therefore feel free to use it in any project or modify the source code.

# Getting Started

## Installation


1. Download the source, just copy all the files in the zip to your OpenCart directory.
2. Click Extensions tab and Payments subtab in your OpenCart admin panel.
3. Find iyzico extension and install the module. Then click Edit.
4. Get your api keys from iyzico merchant backend.
5. Select "Enabled" to activate iyzico plugin for your OpenCart.
6. Select "popup" or "responsive" to display form on checkout page.
7. Define alignment number for the payment sort order.(etc 1,2,3...)
8. User on checkout page will find iyzico payment extension in payment methods.
9. In order details on admin interface, find "iyzico Checkout Form" tab in "Order History" section.
10. From there, admin can Cancel order and/or Full/Partial Refund item.

#### Notice :
If you have installed any other theme on your opencart site, you have to copy below folder from this plugin: **catalog/view/theme/default/template** to your theme  folder at: **catalog/view/theme/current_theme_folder**.

For further information please refer to [iyzico developer portal](https://dev.iyzipay.com).