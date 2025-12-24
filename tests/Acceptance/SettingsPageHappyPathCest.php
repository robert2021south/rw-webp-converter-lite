<?php
namespace tests\Acceptance;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Tests\Support\AcceptanceTester;

class SettingsPageHappyPathCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->executeInSelenium(function(RemoteWebDriver $webDriver) {
            $webDriver->manage()->deleteAllCookies();
        });

        $I->loginAsAdmin();
        $I->amOnAdminPage('index.php');
        $I->see('Dashboard');

        //
        $I->amOnAdminPage('tools.php?page=rwwcl-main&tab=settings');
    }

    private function checkboxes(): array
    {
        return [
            'input[type="checkbox"][name="rwwcl_settings[auto_optimize]"]',
            'input[type="checkbox"][name="rwwcl_settings[keep_original]"]',
            'input[type="checkbox"][name="rwwcl_settings[overwrite_webp]"]',
            'input[type="checkbox"][name="rwwcl_settings[delete_data_on_uninstall]"]',
        ];
    }

    /**
     * Happy Path 1:
     * Admin disables all checkbox settings and they persist
     */
    public function disable_all_settings(AcceptanceTester $I): void
    {
        foreach ($this->checkboxes() as $checkbox) {
            $I->uncheckOption($checkbox);
        }

        $I->click('Save Changes');
        $I->waitForText('Settings saved', 10);

        foreach ($this->checkboxes() as $checkbox) {
            $I->dontSeeCheckboxIsChecked($checkbox);
        }
    }

    /**
     * Happy Path 2:
     * Admin enables all checkbox settings and they persist
     */
    public function enable_all_settings(AcceptanceTester $I): void
    {
        foreach ($this->checkboxes() as $checkbox) {
            $I->checkOption($checkbox);
        }

        $I->click('Save Changes');
        $I->waitForText('Settings saved', 10);

        foreach ($this->checkboxes() as $checkbox) {
            $I->seeCheckboxIsChecked($checkbox);
        }
    }

}
