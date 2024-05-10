<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\CyRunner;
use App\Models\Project;
use App\Models\CyRunnerLog;
use App\Models\ApiRunnerLog;
use App\Models\ApiRunner;
use GuzzleHttp\Client;

class ApiRunnerController extends Controller
{
    public function create(Request $request)
    {
    
        $url = 'https://api.getpostman.com/collections';
        $project = Project::whereId($request->project_id)->first();
        $project_url = $project->postman_payload['project_repo_url'];
        $api_key = $request->api_key;
        $access_token = $project->postman_payload['access_token'];  
         
        $headers = [
            'X-Api-Key: ' . $api_key
        ];
        
        if(!$project){
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found'
            ]);
        }
            
    
        $collection_name = $request->directory_name;
      
        $collections = $this->getCollections($api_key, $url,  $headers);
        
        $collection_files = $this->getCollectionsFiles($collections, $headers, $collection_name);
       
        $responses =  $this->generateCurlCommand($collection_files['collection']['item'],$project_url,  $access_token);
       
        foreach ($responses as $response) {
            $apiRunner = ApiRunner::whereRaw("json_unquote(json_extract(`payload`, '$.\"key\"')) COLLATE utf8mb3_general_ci = '{$response['id']}'")
                ->whereNull('deleted_at')
                ->first();
        
            $payload = [
                'key' => $response['id']
            ];
        
            if ($apiRunner) {
                $apiRunner->update([
                    'project_id' => $project->id,
                    'title' => $response['title'],
                    'group' => $response['group'],
                    'code' => $response['code'],
                    'status' => 'Active',
                    'payload' => json_encode($payload), 
                ]);
            } else {
                ApiRunner::create([
                    'project_id' => $project->id,
                    'title' => $response['title'],
                    'group' => $response['group'],
                    'code' => $response['code'],
                    'status' => 'Active',
                    'payload' => json_encode($payload), 
                ]);
              
            }
    }
    return "Files updated/created successfully";
        
    }    


    public function run(Request $request) {
        // $curlCommand = "curl -X POST -H 'Accept: application/json' -F 'ftp_key=ftp-api-FHetoA2bd6GV9VG1GHGJ63GBCSDRT7FC8FFD2HGFHGDSDvbsd6a' 'https://zterminal.dze-labs.xyz/api/zterminal/project/users'";  
        $apiRunner = ApiRunner::where('id', $request->api_runner_id)->where('status', 'Active')->first();
        if(!$apiRunner){
            return response()->json([
               'status' => 'error',
               'message' => 'ApiRunner not found'
            ]);
        }

        $curl_response = $this->executeCurlCommand($apiRunner->code);

         // Create ApiRunnerLog entry
         $response =  ApiRunnerLog::create([
            'group_id' => $apiRunner->project_id,
            'api_runner_id' => $apiRunner->id,
            'user_id' => $apiRunner->user_id,
            'payload' => $curl_response,
            'status' => 'Completed',
            'result' => 'Pass',
        ]);

        $apiRunnerResponse = ApiRunner::where('id', $response->api_runner_id)->where('status', 'Active')->first();
        $projectResponse = Project::where('id', $apiRunnerResponse->project_id)->first();


        return [
            'api_runner' => $apiRunnerResponse,
            'project_response' => $projectResponse,
            'response' => $response,
        ]; 
      }


      function generateCurlCommand($items, $project_url, $access_token, $subfolder = 'Unorganized')
    {
        $curlCommands = [];

        foreach ($items as $item) {
            $name = isset($item['name']) ? $item['name'] : '';
            $id = isset($item['uid']) ? $item['uid'] : '';
            $request = isset($item['request']) ? $item['request'] : null;

            if ($request && isset($request['method'])) {
                $curlCommand = "curl -X {$request['method']}";

                // Check if the request requires authentication
                $authenticationRequired = isset($request['auth']);

                if ($authenticationRequired) {
                    // Add the Authorization header with the access token
                    $curlCommand .= " -H 'Authorization: Bearer {$access_token}'";
                }

                if (isset($request['header'])) {
                    foreach ($request['header'] as $header) {
                        // Ensure 'key' and 'value' are set before accessing them
                        if (isset($header['key'], $header['value'])) {
                            $curlCommand .= " -H '{$header['key']}: {$header['value']}'";
                        }
                    }
                }

                if (isset($request['body'])) {
                    $bodyMode = isset($request['body']['mode']) ? $request['body']['mode'] : '';
                    if ($bodyMode === 'formdata' && isset($request['body']['formdata'])) {
                        foreach ($request['body']['formdata'] as $formData) {
                            // Ensure 'key' and 'value' are set before accessing them
                            if (isset($formData['key'], $formData['value'])) {
                                $curlCommand .= " -F '{$formData['key']}={$formData['value']}'";
                            }
                        }
                    }
                }

                if (isset($request['url']['raw'])) {
                    $url = str_replace('{{base_url}}', $project_url, $request['url']['raw']);
                    $curlCommand .= " '{$url}'";
                }

                $curlCommands[] = [
                    'id' => $id,
                    'group' => $subfolder,
                    'title' => $name,
                    'code' => $curlCommand
                ];
            }

            if (isset($item['item']) && is_array($item['item'])) {
                $parentFolder = isset($item['name']) ? $item['name'] : '';
                $curlCommands = array_merge($curlCommands, $this->generateCurlCommand($item['item'], $project_url, $access_token, $parentFolder));
            }
        }

        return $curlCommands;
    }

      


    public function getCollections($api_key, $url,  $headers) {
       
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        // Disable SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_status !== 200) {
                echo 'HTTP error: ' . $http_status;
            } else {
                var_dump($response); // Debugging: Output response to see if it contains any useful information
            }
        }
        
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function getCollectionsFiles($collections, $headers, $collection_name) {
        if (isset($collections['collections'])) {
            foreach ($collections['collections'] as $collection) {
                if ($collection['name'] === $collection_name) {
                    $collection_id = $collection['uid'];
                    
                     $url = 'https://api.getpostman.com/collections/' . $collection_id;
                    
                    $ch = curl_init();
                    
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    
                    // Disable SSL certificate verification
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    
                    $response = curl_exec($ch);
                    
                    if(curl_errno($ch)){
                        echo 'Curl error: ' . curl_error($ch);
                        return false;
                    }
                    
                    curl_close($ch);
                    
                    return json_decode($response, true);
                }
            }
            echo "Error: Collection not found.";
            return false;
        } else {
            echo "Error: Unable to fetch collections.";
            return false;
        }
    }

    // function generateCurlCommand($requestData, $project_url) {
    //     // Extract request method
    //     $method = strtoupper($requestData['request']['method']); // Ensure method is uppercase
    
    //     // Extract headers
    //     $headers = [];
    //     foreach ($requestData['request']['header'] as $header) {
    //         $headers[] = "-H '{$header['key']}: {$header['value']}'"; // Format header correctly
    //     }
    //     $headersString = implode(" ", $headers);
    
    //     // Extract body parameters
    //     $body = "";
    //     if (isset($requestData['request']['body']) && isset($requestData['request']['body']['formdata'])) {
    //         $formData = $requestData['request']['body']['formdata'];
    //         foreach ($formData as $data) {
    //             $body .= "-F '{$data['key']}={$data['value']}' "; // Format form data correctly
    //         }
    //     }
    
    //     // Extract URL
    //     $url = $requestData['request']['url']['raw'];
    
    //     // Replace placeholders in the URL with project URL
    //     $url = str_replace("{{base_url}}", $project_url, $url);
    
    //     // Assemble cURL command
    //     $curlCommand = "curl -X $method $headersString $body '$url'";
    
    //     return $curlCommand;
    // }

    
    public function collectionFileResponse($collection_files, $file_name = null) {
        
        if (!empty($collection_files) && isset($collection_files['collection']['item'])) {
            $collections_files = $collection_files['collection']['item'];
            $matchingFiles = [];
            foreach ($collections_files as $curl_file) {
                if (isset($curl_file['name']) && $curl_file['name'] == $file_name) {
                     $matchingFiles[] = $curl_file; // Return the matching file if found
                     return $matchingFiles;
                    } else {
                     $matchingFiles[] = $curl_file; // Store curl_file_code if the name doesn't match
                } 
            }
            if (empty($file_name)) {
                return $matchingFiles;
            }
        } else {
            return "Error: Unable to fetch collection files.\n";
        }
        
        return null; // Return null if file not found
    }
    
    public function executeCurlCommand($curlCommand) {
        // Parse the cURL command
        preg_match_all('/(?:\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'|\"([^\"\\\\]*(?:\\\\.[^\"\\\\]*)*)\")/', $curlCommand, $matches);
        $url = $matches[1][count($matches[1]) - 1]; // Last matched string is the URL
        $method = 'GET'; // Default method is GET
    
        // Check if the command contains a method
        if (preg_match('/-X\s+([^\s]+)/', $curlCommand, $methodMatch)) {
            $method = strtoupper($methodMatch[1]);
        }
    
        $headers = array();
        $postData = '';
    
        // Check for headers and data
        foreach ($matches[1] as $match) {
            if (strpos($match, 'http') !== 0) {
                // Headers or data
                if (strpos($match, ':') !== false) {
                    // Headers
                    $headers[] = $match;
                } else {
                    // Data
                    $postData .= $match . '&';
                }
            }
        }
    
        // Initialize cURL session
        $ch = curl_init();
    
        // Set the URL
        curl_setopt($ch, CURLOPT_URL, $url);
    
        // Set the request method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        // Disable SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        // Set additional headers, if any
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
    
        // Set the data to send, if any
        if (!empty($postData)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim($postData, '&'));
        }
    
        // Return the response instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Execute the request
        $response = curl_exec($ch);
    
        // Check for errors
        if ($response === false) {
            // If there is an error, you can handle it here
            $error = curl_error($ch);
            // Example: throw new Exception("cURL error: " . $error);
            curl_close($ch);
            return "Error: $error";
        }
        $status_code = 0;
    
        // Check HTTP status code
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch ($httpStatusCode) {
            case 200:
                $status_code = 200;
                break;
            case 201:
                $status_code = 201;
                break;
            case 204:
                $status_code = 204;
                break;
            case 400:
                $status_code = 400;
                $response = "Error 400: Bad Request";
                break;
            case 401:
                $status_code = 401;
                $response = "Error 401: Unauthorized";
                break;
            case 411:
                $status_code = 401;
                $response = "Error 401: Unauthorized";
                break;
            case 403:
                $status_code = 403;
                $response = "Error 403: Forbidden";
                break;
            case 404:
                $status_code = 404;
                $response = "Error 404: Not Found";
                break;
            case 405:
                $status_code = 405;
                $response = "Error 405: Method Not Allowed";
                break;
            case 500:
                $status_code = 500;
                $response = "Error 500: Internal Server Error";
                break;
            default:
            $response = "Error: Unexpected HTTP status code $httpStatusCode";
            break;
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Return the response
        return [
           'status_code' => $status_code,
           'response' => $response,
        ];
    }
    
    
    

    function runRequestFromJson($jsonData, $project_url) {

        $name = $jsonData['name'];
        $method = $jsonData['request']['method'];
        $replace_url = $jsonData['request']['url']['raw'];

        $url = str_replace('{{base_url}}', $project_url, $replace_url);
        $headers = array();
        foreach ($jsonData['request']['header'] as $header) {
            $headers[] = $header['key'] . ': ' . $header['value'];
        }
        $formData = array();
        foreach ($jsonData['request']['body']['formdata'] as $item) {
            $formData[$item['key']] = $item['value'];
        }
    
        // Initialize cURL handle
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Disable SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        // Check if the URL is valid
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            // If URL is invalid, handle the error
            echo 'Error: Invalid URL';
            return false;
        }
    
        // If sending data with the request
        if (!empty($formData)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));
        }
    
        // Execute cURL request
        $response = curl_exec($ch);
    
        // Check for errors
        if ($response === false) {
            // If there's an error, you might want to handle it accordingly
            echo 'Error: ' . curl_error($ch);
        }
    
        // Close cURL resource
        curl_close($ch);
    
        // Return API response
        return [
            'message' => 'Success',
            'response'=> $response,
            'file_name' => $name,
        ];
    }
    
    
   
    public function errorTaskCreate($project_register_id, $error_msg, $request_link)
    {
        return "s";
        $client = new Client();
    
        $headers = [
            'Accept' => 'application/json',
        ];
    
        $options = [
            'multipart' => [
                [
                    'name' => 'project_register_id',
                    'contents' => $project_register_id,
                ],
                [
                    'name' => 'error_msg',
                    'contents' => $error_msg,
                ],
                [
                    'name' => 'request_link',
                    'contents' => $request_link,
                ],
            ],
            'verify' => false, // Enable SSL certificate verification
        ];
    
        $response = $client->post('https://hq.defenzelite.com/api/v1/task/add-exception', [
            'headers' => $headers,
            'multipart' => $options['multipart'],
            'verify' => false,
        ]);
    
        // Check if the request was successful
        if ($response->getStatusCode() == 200) {
            // Request was successful
            return true;
        } else {
            // Request failed, handle the error
            return false;
        }
    }
    

    
    

    

}
