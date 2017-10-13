<?php

namespace Reboot;

use GuzzleHttp\Client;

class Barclaycard{

	protected $pw;
	protected $pspid;

	public $order_id;
	public $amount;

	protected $user_name;
	protected $password;
	protected $shasign;
	protected $rawdata;

	protected $dataToGo=[];
	protected $payemntUrl;
	protected $bankResponse;

	public $error=[];


	function __construct($pspid,$user_name, $password, $pw, $url) {		
		$this->pw=$pw;
		$this->payemntUrl=$url;
		$this->dataToGo = [ 
					'PSPID'=>$pspid, 
					'USERID'=> $user_name, 
					'PSWD'=>$password
		];

	}

	public function amount($amount,$currency ) {				
		$this->dataToGo['AMOUNT']=bcmul($amount, 100); 
		$this->dataToGo['CURRENCY']=$currency;		
		return $this;						
	}



	public function card( $expiry_date, $card_number, $card_name, $cvc ){		
		$this->dataToGo['CARDNO']=$card_number; 
		$this->dataToGo['ED']=$expiry_date; 
		$this->dataToGo['CN']=$card_name; 
		$this->dataToGo['CVC']=$cvc; 											
		return $this;
	}


	# assign order_id
	public function orderId($order_id){
		$this->dataToGo['ORDERID'] = $order_id;
		return $this;
	}


	protected function shastring($email=null, $address_1=null, $phone=null, $town=null, $postcode=null){

		# create string to hash (digest) using values of options/details above
		$shaData =
			"AMOUNT=" . $this->dataToGo['AMOUNT'] . $this->pw .
			"CARDNO=" . $this->dataToGo['CARDNO'] . $this->pw .
			"CN=" . $this->dataToGo['CN']  . $this->pw .
			"CURRENCY=" . $this->dataToGo['CURRENCY'] . $this->pw .	
			"CVC=" . $this->dataToGo['CVC'] . $this->pw .
			"ED=" . $this->dataToGo['ED'] . $this->pw ;

			$email ? $shaData .= "EMAIL=" . $email . $this->pw : false;
			$shaData .= "ORDERID=" . $this->order_id . $this->pw ;
			$address_1 ? $shaData .=	"OWNERADDRESS=" . $address_1 . $this->pw : false;
			$phone ? $shaData .=	"OWNERTELNO=" . $phone . $this->pw : false;
			$town ? $shaData .=	"OWNERTOWN=" . $town . $this->pw : false;
			$postcode ? $shaData .=	"OWNERZIP=" . $postcode . $this->pw : false;
			
			$shaData .=
			"PSPID=" . $this->dataToGo['PSPID'] . $this->pw .
			"PSWD=" . $this->dataToGo['PSWD']  . $this->pw .
			"USERID=" . $this->dataToGo['USERID'] . $this->pw ;

		$this->shasign = strtoupper(sha1($shaData));
		$this->rawdata=strtoupper($shaData);

		return $this;
	}

	# build array of data to send
	public function customer($email=null, $phone=null, $address_1=null,  $town=null, $postcode=null){

		$email ? $this->dataToGo['EMAIL']= $email : false;
		$address_1 ?$this->dataToGo['OWNERADDRESS']= $address_1 : false;
		$postcode ? $this->dataToGo['OWNERZIP']= $phone : false;
		$town ? $this->dataToGo['OWNERTOWN']= $town : false;
		$phone ? $this->dataToGo['OWNERTELNO']= $postcode: false;
	
		$this->shastring(	
							($email ? $email: false), 
							($address_1 ? $address_1:null), 
							($phone ? $phone:null), 
							($town ? $town:null), 
							($postcode ? $postcode:null)
						);
			
		
		$this->dataToGo['SHASIGN']=$this->shasign;
		$email ? $this->dataToGo['EMAIL']= $email : false;		
		$address_1 ? $this->dataToGo['OWNERADDRESS']= $address_1 : false;		
		$phone ? $this->dataToGo['OWNERZIP']= $phone : false;
		$town ?  $this->dataToGo['OWNERTOWN']= $town :false;
		$postcode ? $this->dataToGo['OWNERTELNO']= $postcode :false;
		
		   
		 return   $this;
		
	}


	public function pay(array $paymentData=null){

		if ($paymentData) 
		{
			
			$this->dataToGo['AMOUNT']=bcmul($paymentData['amount'], 100); 
			$this->dataToGo['CURRENCY']=$paymentData['currency'];		
			$this->dataToGo['CARDNO']=$paymentData['card_number']; 
			$this->dataToGo['ED']=$paymentData['expiry_date']; 
			$this->dataToGo['CN']=$paymentData['card_name']; 
			$this->dataToGo['CVC']=$paymentData['cvc']; 
			$this->dataToGo['ORDERID'] = $paymentData['order_id'];
			isset($paymentData['email'])? $this->dataToGo['EMAIL']= $paymentData['email'] : false;
			isset($paymentData['address']) ? $this->dataToGo['OWNERADDRESS']= $paymentData['address'] : false;
			isset($paymentData['postcode']) ? $this->dataToGo['OWNERZIP']= $paymentData['postcode'] : false;
			isset($paymentData['town']) ? $this->dataToGo['OWNERTOWN']= $paymentData['town'] : false;
			isset($paymentData['postcode']) ? $this->dataToGo['OWNERTELNO']= $paymentData['postcode'] : false;

		}
										
		$this->sendPaymentData();
		return $this;
		
	}

	protected function sendPaymentData(){

		$client = new Client();
		try {
		   $res = $client->request('POST', $this->payemntUrl, [ 'form_params' => $this->dataToGo ]);
		   if ($res->getStatusCode() ==200){

				$this->bankResponse =  $res->getBody();
				
			}
		}
		catch (GuzzleHttp\Exception\ClientException $e) {
		    $this->error['response'] = $e->getResponse();
		    $this->error['responseBodyAsString'] = $response->getBody()->getContents();

		}

		return $this;


	}

	public function response(){

			// str_replace(array("\r\n", "\n\r", "\n", "\r"), ' ', ); 

		return $this->bankResponse;
	}



}





	