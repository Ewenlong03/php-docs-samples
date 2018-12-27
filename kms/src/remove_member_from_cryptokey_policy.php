<?php
/**
 * Copyright 2017 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * For instructions on how to run the full sample:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/kms/README.md
 */


// Include Google Cloud dependendencies using Composer
require_once __DIR__ . '/../vendor/autoload.php';

if (count($argv) != 7) {
    return printf("Usage: php %s PROJECT_ID KEYRING_ID CRYPTOKEY_ID MEMBER ROLE LOCATION_ID\n", basename(__FILE__));
}
list($_, $projectId, $keyRingId, $cryptoKeyId, $member, $role, $locationId) = $argv;


# [START kms_add_member_to_keyring_policy]
use Google\Cloud\Kms\V1\KeyManagementServiceClient;
use Google\Cloud\Iam\V1\Binding;

/** Uncomment and populate these variables in your code */
// $projectId = 'The Google project ID';
// $keyRingId = 'The KMS key ring ID';
// $cryptoKeyId = 'The KMS key ID';
// $member = 'Must be in the format "user:$userEmail" or "serviceAccount:$serviceAccountEmail"';
// $role = 'Must be in the format "roles/$role", "organizations/$organizationId/roles/$role", or "projects/$projectId/roles/$role"';
// $locationId = '';

$kms = new KeyManagementServiceClient();

// The resource name of the CryptoKey.
$cryptoKeyName = $kms->keyRingName($projectId, $locationId, $keyRingId, $cryptoKeyId);

// Get the current IAM policy and remove the account to it.
$policy = $kms->getIamPolicy($cryptoKeyName);
foreach ($policy->getBindings() as $binding) {
    if ($binding->getRole() == $role) {
        $members = $binding->getMembers();
        foreach ($members as $i => $existingMember) {
            if ($member == $existingMember) {
                unset($members[$i]);
                $binding->setMembers($members);
                break;
            }
        }
    }
}

// Set the new IAM Policy.
$kms->setIamPolicy($cryptoKeyName, $policy);

printf('Member %s removed from policy for cryptoKey %s in keyRing %s' . PHP_EOL,
    $member,
    $cryptoKeyId,
    $keyRingId);
# [END kms_add_member_to_keyring_policy]
