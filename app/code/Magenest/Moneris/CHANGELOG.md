# Change Log
All notable changes to this extension will be documented in this file.
This extension adheres to [Magenest](http://magenest.com/).

## [2.2.0] - 2019-02-13
-   Moneris now compatible with Magento 2.3
-   fix CVD box not showing in frontend
-   PartialCapture: disable partial capture, show clear message to user
-   VaultCapture: rework vault capture
-   VaultAuthorize: add vault authorize
-   Vault: remove un-used GetNonce
-   Fixbug: add error handle from moneris
-   FixBug: prevent arrow key action, add validations
-   Add validation in month, year inputs
-   Update: show AVS/CVD error on checkout page
-   Fix bug can't save card after deleting its previous one

## [2.1.0] - 2017-12-30
## Update
1. Add Saved Card Payment Checkout
2. Fix some bugs

## [2.0.0] - 2017-10-24
### Add
1. Compatible with Magento 2.2
2. Add addresses to redirect connection

## [1.1.0] - 2017-08-14
### Update
1. Add Hosted Payment Checkout for Canada
2. Add Hosted Payment Checkout for US


## [1.0.1] - 2017-05-07
### Update
1. Add AVS (Address Verification Service) Check
2. Add CVD (Card Validation Digits) Check
3. Admin can enable/disable performing AVS, CVD check on customer's billing address, credit card card verification number
4. Admin can choose Payment Action (Accept, Hold, Reject) for each AVS check status
5. Admin can choose Payment Action (Accept, Hold, Reject) for each CVD check status

## [1.0.0] - 2017-01-07
### Releases
1. Allow customers to checkout using Moneris Payment Gateway
2. Allow admins to easily tweak and manage payments via Moneris
3. Allow create transaction type: capture, authorize, Refund
4. Allow create new order in the admin panel using Moners Payment Gateway 
5. In current version, only support Direct connection type
