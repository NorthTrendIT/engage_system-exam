<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\SalesPerson;

class SAPSalesPersons
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

	protected $database;
	protected $username;
	protected $password;

    public function __construct($database, $username, $password)
    {
        $this->headers = $this->cookie = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->headers['Cookie'] = $this->authentication->getSessionCookie();

        $this->httpClient = new Client();
    }

    // Get Sales Persons data
    public function getSalesPersonsData($url = '/b1s/v1/SalesPersons')
    {
    	try {
            $response = $this->httpClient->request(
                'GET',
                env('SAP_API_URL').$url,
                [
                    'headers' => $this->headers,
                    'verify' => false,
                ]
            );

            if(in_array($response->getStatusCode(), [200,201])){
                $response = json_decode($response->getBody(),true);

                return array(
                                'status' => true,
                                'data' => $response
                            );
            }

        } catch (\Exception $e) {
            return array(
                                'status' => false,
                                'data' => []
                            );
        }
    }

    // Store All Customer Records In DB
    public function addSalesPersonsDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getSalesPersonsData($url);
        }else{
            $response = $this->getSalesPersonsData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                foreach ($data['value'] as $value) {

                    $insert = array(
                                    'sales_employee_code' => $value['SalesEmployeeCode'],
                                    'sales_employee_name' => $value['SalesEmployeeName'],
                                    'remark' => $value['Remarks'],
                                    'commission_for_sales_employee' => $value['CommissionForSalesEmployee'],
                                    'commission_group' => $value['CommissionGroup'],
                                    'locked' => $value['Locked'],
                                    'employee_id' => $value['EmployeeID'],
                                    'is_active' => $value['Active'] == "tYES" ? true : false,
                                    'u_manager' => $value['U_MANAGER'],
                                    'u_position' => $value['U_POSITION'],
                                    'u_initials' => $value['U_INITIALS'],
                                    'u_warehouse' => $value['U_WAREHOUSE'],
                                    'u_password' => $value['U_Password'],
                                    'u_area' => $value['U_AREA'],
                                    //'response' => json_encode($value),
                                );

                    $obj = SalesPerson::updateOrCreate(
                                            [
                                                'sales_employee_code' => @$value['SalesEmployeeCode'],
                                            ],
                                            $insert
                                        );
                }

                if($data['odata.nextLink']){
                    $this->addSalesPersonsDataInDatabase($data['odata.nextLink']);
                }
            }
        }
    }
}
