[![Dev](https://github.com/diamondicq/ecommerce2/actions/workflows/dev.yml/badge.svg)](https://github.com/diamondicq/ecommerce2/actions/workflows/dev.yml)  [![Testing](https://github.com/diamondicq/ecommerce2/actions/workflows/testing.yml/badge.svg)](https://github.com/diamondicq/ecommerce2/actions/workflows/testing.yml)  [![Staging](https://github.com/diamondicq/ecommerce2/actions/workflows/version_and_merge.yml/badge.svg)](https://github.com/diamondicq/ecommerce2/actions/workflows/version_and_merge.yml)

## Programming Best Practices
You should follow common programming best practices to reduce bugs and improve the quality and maintainability of your work.

Read the documentation: https://devdocs.magento.com/

Recommended reading: [Complete Guide on Magento 2 Development Best Practices](https://www.icecubedigital.com/blog/complete-guide-on-magento-2-development-best-practices)

## Work process
This is basically the work process we follow https://guides.github.com/introduction/flow/
The status flow for the task that you are working on should be:
Todo ➝ In progress ➝ PR Submitted ➝ PR Approved (Deploy to testing env) ➝ Ready for QA ➝ QA Passed ➝ Version it and Merged to Staging ➝ Rebase testing branch to staging (resolve any conflicts with the authors of the affected code)

## Testing environment
- On each push to GitHub dev branch it auto deploys to https://dev.dicqinfotech.com/

If you need to test your changes you can push to the dev above so they get deployed. When pushing to the branch, if the previous push was recent then check with the owner of the commit if they have finished testing to avoid overwriting someone else's work.

## The Testing environment
The testing branch is for QA, after your PR is approved deploy changes to testing for QA to test it. After it is QA passed then you can version it and merge to staging.
- The testing branch auto deploys to https://testing.dicqinfotech.com

## Staging environment
The staging branch is for QA, we should strive to keep it as stable as the production environment.
- The staging branch auto deploys to https://staging.dicqinfotech.com

## Working Branch
Please use your own working branch when working on an issue. Remember to always rebase with the staging branch.

## Semantic Commit Messages
For your commit names, you must follow the guideline here https://gist.github.com/joshbuchea/6f47e86d2510bce28f8e7f42ae84c716 and include the task ID in the commit message.

## Pull request
Properly format your code using PHP Intelephense and Prettier.

Please check your code by running GrumPHP for any issues and try to fix as many as possible without creating significant overheads on the task that you were working on.

See [Why You Need Code Sniffers for Web Development](https://www.hongkiat.com/blog/code-optimization-code-sniffers/#:~:text=Code%20sniffers%20will%20check%20source,the%20way%20code%20is%20written).

Run: `composer grum-changed`

After you have tested and are satisfied with the work in your working branch, submit a pull-request against the staging branch.

All pull-requests need to have at least one approval before been merged to staging.

## Merge to staging and versioning
It is the responsibility of the PR owner to merge their own PR.
