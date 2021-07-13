# googleapi-import-export
Import and exports data from and in magento
# Documention steb by step
1. Create a spreadsheet in Google Drive with to sheet(`Import` and `Export`) and share it externally
2. Visit https://console.developers.google.com
3. Create a new project
4. Tap on `Enable APIs And Services`
5. Search for `Google Sheets API` and enable it
6. Go to `Apis and services` and create a `Service Accounts`
7. Add a `Key` and download the json file with the credentials
8. Install the module by composer by doing:
   a. Add this to the `repositories`:
   `{
   "type": "vcs",
   "url": "https://github.com/atty31/googleapi-import-export.git"
   }`
   b. `composer require atma/module-products:dev-master`
9. Execute:
   `bin/magento setup:upgrade && bin/magento setup:di:compile && bin/magento c:c && bin/magento c:f`
10. Go to Store->Configurations->Atma->General Configuration
11. Add the json file content (see step 7) in the `Google Api Details` field
12. Add the spreadsheet it (You can get it from the sharable url) to the `SpreadSheet Id` field
13. Add the sheet name for export to the `Export sheet name` field (see step 1)
14. Add the sheet name for import  to the `Import sheet name` field (see step 1)
15. Make sure, you respect this header: sku, product_type,name,description,status,visibility,price,is_in_stock,qty. Otherwise it will not work!
16. Enjoy!