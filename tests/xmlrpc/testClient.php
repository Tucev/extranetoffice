<?php
// Include PHP XML-RPC library
include("lib/xmlrpc.inc");

// If the form has beeen submitted we proceed to send the XML-RPC request
if (isset($_POST["method_name"]) && $_POST["method_name"] != "") {
    // Get post vars from request
    $method_name = (string) $_POST["method_name"];
    $param = $_POST["param"];

    // Ignore the post vars and create hard-coded value
    $xmlrpc_val = new xmlrpcval();

    // Create structure to nest
    $sub_struct = new xmlrpcval();
    $height = new xmlrpcval(3, "int");
    $width = new xmlrpcval(4, "int");
    $depth = new xmlrpcval(3, "int");
    $sub_array = array('height'=>$height,'width'=>$width, 'depth'=>$depth);
    $sub_struct->addStruct($sub_array);
    
    // Make an assoc array of xmlrpcval objects    
    $xmlrpc_val_a = new xmlrpcval('fast');
    $xmlrpc_val_b = new xmlrpcval(2009, "int");

    $assoc_array = array('model'=>$xmlrpc_val_a,'year'=>$xmlrpc_val_b, 'detail'=>$sub_struct);
    $xmlrpc_val->addStruct($assoc_array);

    // Instantiate XML-RPC message object
    $xmlrpc_msg = new xmlrpcmsg( $method_name, array($xmlrpc_val));
        
    print "<pre>Sending the following request:\n\n" . htmlentities($xmlrpc_msg->serialize()) . "\n\nDebug info of server data follows...\n\n</pre>";
    //exit;
    
    // Instantiate XML-RPC client object used to send the XML-RPC message
    $xmlrpc_client = new xmlrpc_client("/extranetoffice/src/", "localhost", 80);
    
    // Uncomment line below to enable debugging
    $xmlrpc_client->setDebug(1);
    
    // Send XML-RPC request and capture response
    $xmlrpc_response =& $xmlrpc_client->send($xmlrpc_msg);
    
    if (!$xmlrpc_response->faultCode()) {
        $xmlrpc_value = $xmlrpc_response->value();
        
    //var_dump($xmlrpc_value); exit;
        
        switch ($xmlrpc_value->kindOf()) {
            case 'scalar' : // scalar includes boolean, int, float, string and base64
                // If the return is a base64 scalar we assume it is the jpeg...
                if ($xmlrpc_value->scalarTyp() == "base64") {
                    header("Content-Type: image/png");
                    echo $xmlrpc_value->scalarval(); exit;
                }
                
                $translated_response_value = htmlspecialchars($xmlrpc_value->scalarval());
                break;
            
            case 'array' :
                // iterating over values of an array object
                for ($i=0; $i<$xmlrpc_value->arraySize(); $i++) {
                    $array_item = $xmlrpc_value->arrayMem($i);
                    $translated_response_value[] = $array_item->scalarval();
                }
                break;
                
            case 'struct' :
                $translated_response_value = $xmlrpc_value->scalarval();
                break;
        }
    }
    else {
        $translated_response_value = "XML-RPC Error - ";
        $translated_response_value .= "Code: ".htmlspecialchars($xmlrpc_response->faultCode());
        $translated_response_value .= " Reason: '" . htmlspecialchars($xmlrpc_response->faultString()) . "'";
    }
} 
else {
    $param = "";
}
?>

<html>
<head><title>xmlrpc</title></head>
<body>

<h1>XML-RPC Client for testing (ignore the form; i do)</h1>
<form action="testClient.php" method="POST">
    Method to call on server:
    <select name="method_name">
        <option value="projects.display" <?php if ($method_name == 'projects.display') echo 'selected'; ?>>
            projects.display
        </option>
    </select>
    <br/>
    Key:
    <input name="key" value="<?php echo $key; ?>">
    <br/>
    Value:
    <input name="value" value="<?php echo $value; ?>">
    <br/>
    <input type="submit" value="go" name="submit">
</form>

<div>
<?php if ($translated_response_value) : ?>
    Response from server is:<br />
    <?php var_dump($translated_response_value); ?>
<?php endif; ?>
</div>

</body>
</html>
