<?php

/**
 * Description of ActiveCampaignTest
 *
 * @author Bryan J. Agee <bryan@pamiris.com>
 */
class ActiveCampaignTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ActiveCampaign
     */
    private $activeCampaign;
    
    /**
     * This spawns an instance via reflection so that we can test methods 
     * that don't require api access/full path to operate
     */
    protected function setUp()
    {
        parent::setUp();
        $reflection = new ReflectionClass('ActiveCampaign');
        $this->activeCampaign = $reflection->newInstanceWithoutConstructor();
    }
    
    /**
     * Used to create valid instance of ActiveCampaign
     * 
     * @param type $url
     * @param type $apiKey
     * @param type $apiUser
     * @param type $apiPassword
     */
    protected function bootstrapActiveCampaign($url, $apiKey, $apiUser = "", $apiPassword = "")
    {
        $this->activeCampaign = new ActiveCampaign($url, $apiKey, $apiUser, $apiPassword);
    }
    
    /**
     * 
     * @dataProvider getComponentFromPathProvider
     */
    public function testGetComponentFromPath($path, $expectedComponent)
    {
        $component = $this->activeCampaign->getComponentFromPath($path);
        $this->assertEquals($expectedComponent, $component);
    }
    
    /**
     * 
     * @dataProvider getMethodFromPathProvider
     */
    public function testGetMethodromPath($path, $expectedMethod)
    {
        $method = $this->activeCampaign->getMethodFromPath($path);
        $this->assertEquals($expectedMethod, $method);
    }
    
    /*
     * DataProvider methods
     */
        
    public static function getComponentFromPathProvider()
    {
        return [
            ['contact/tag/add?whatever','contact'],
            ['singlesignon/method?cred=something','auth'],
            ['branding/view','design'],
            ['account/edit','account'],
        ];
    }
    
    public static function getMethodFromPathProvider()
    {
        return [
            ['sync/?params', 'sync'],
            ['contact/tag/add', 'tag_add'],
            ['contact/tag_add?someparams', 'tag_add'],
        ];
    }
    
    
}
