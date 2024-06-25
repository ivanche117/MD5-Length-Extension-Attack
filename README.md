# MD5-Length-Extension-Attack
Exploring the MD5 length extension vulnerability, demonstrating how to extend messages without knowing the original secret key.

___
# Table of contents:
- Introduction
- Project Structure
- Description
- Example Walkthrough
- Modifying the Code
- Conclusion

___

## Introduction


This project demonstrates an MD5 length extension attack, highlighting how an attacker can manipulate a hash and extend the content of the file **logs.txt** without knowing the original secret key. The secret key used in this demonstration is '**secret**'.
___
## Project Structure
- **MD5_LEA.php:** Contains the backend logic for performing the MD5 length extension attack
- **logs.txt:** A log file that person A wants to send to person B. The data in this file is the original data.
___
## Description

In this project, the attacker intercepts the file **logs.txt** and its hash sent from person A to person B. Without knowing the secret key, the attacker performs a length extension attack (LEA) to produce a new hash and extend the original data of the file. The attacker then sends the new hash and extended file to person B. Upon verification, person B hashes the new modified file with the secret key and obtains the same hash as the attacker, thus validating the file and being unaware of any changes made.
___
## Example Walkthrough

### Original file data and hash:

- Person A has the file **logs.txt** with its content:

```
    2023-06-16 12:00:00: User1 logged in
    2023-06-16 12:05:00: User1 performed action A
    2023-06-16 12:10:00: User1 logged out
    
    2023-06-17 13:04:02: User2 logged in
    2023-06-17 13:07:00: User1 logged in
    2023-06-17 13:11:00: User2 performed action B
    2023-06-17 13:13:40: User2 performed action C
    2023-06-17 13:14:00: User1 updated profile
    2023-06-17 13:23:02: User2 logged out
    2023-06-17 13:27:00: User1 logged out
    
    2023-06-18 12:04:20: User7 logged in
    2023-06-18 13:16:34: User3 logged in
    2023-06-18 14:07:00: User4 logged in
    2023-06-18 14:08:00: User4 viewed dashboard
    2023-06-18 14:15:00: User4 logged out
```

    
- Person A decides to hash it with the secret key **'secret'** using MD5 hash function and generates the original hash:

  ``` Original Hash: H(secret || original_data) : bf1c03ff4510d5f8bd121ad151863c64```

- Person A sends both the file and the hash, but they get intercepted by the attacker.

### Performing Length extension Attack:
- The attacker uses **MD5_LEA.php** to perform the Length extension Attack.
- The attacker notices that User7 remained logged in, so the data the attacker wants to append is:

``` 2023-06-18 15:30:00: User7 performed action D```

- So, the attacker finds the length of the secret key (using brute force) and crafts a new modified file that includes the original file data, padding and the new data he wants to append:

```
    2023-06-16 12:00:00: User1 logged in
    2023-06-16 12:05:00: User1 performed action A
    2023-06-16 12:10:00: User1 logged out
    
    2023-06-17 13:04:02: User2 logged in
    2023-06-17 13:07:00: User1 logged in
    2023-06-17 13:11:00: User2 performed action B
    2023-06-17 13:13:40: User2 performed action C
    2023-06-17 13:14:00: User1 updated profile
    2023-06-17 13:23:02: User2 logged out
    2023-06-17 13:27:00: User1 logged out
    
    2023-06-18 12:04:20: User7 logged in
    2023-06-18 13:16:34: User3 logged in
    2023-06-18 14:07:00: User4 logged in
    2023-06-18 14:08:00: User4 viewed dashboard
    2023-06-18 14:15:00: User4 logged out

    0000000000000

    2023-06-18 15:30:00: User7 performed action D
```


- Additionaly the attacker hashes the new modified data and generates a new hash, different from the original hash:

  ```New Hash: H(secret || original_data || padding || data_to_append) : 3cc7255ae2b291fb4488ee01fff2e5dc```




### Validation by Person B:
- Person B recieves the file and the hash from the attacker (believing Person A sent them), without knowing that the file has been modified and that a new hash has been generated.
- Person B hashes the recieved file with the secret key **'secret'**:
  
  ```Verification Hash: 3cc7255ae2b291fb4488ee01fff2e5dc```
- The hashes match, so person B believes the message is unaltered.

  ```Match: True```

___
## Modifying the code
To run the project you need to update the path of the **"logs.txt"** file in the **"MD5_LEA.php"** script, to the correct location on your PC. Open **"MD5_LEA.php"** and modify line **140** to reflect the path on your system. 

Make sure to replace ```$file_path = "path/to/your/logs.txt"``` with the actual path where **"logs.txt"** is located on your machine.
___
## Conclusion
This project highlights the vulnerability in MD5 hash functions concerning length extension attacks. It serves as a demonstration of how important it is to use more secure hash functions and implement additional security measures when validating data integrity.







