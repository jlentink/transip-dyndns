Single more handy binary can be found a: https://github.com/jlentink/go-transip-dyndns

#Transip Dyndns script

##usage

```
$ transip-dyndns.php  --username <username> --private-key <path to private key file> --domain <domain>
```

### Example

```
$ transip-dyndns.php  --username myusername --private-key /home/foo/key.pem --domain foo.bar.com
```


##Options

*--username | -u* The transip username

*--private-key | -p* Path to a text file containting the Transip private key

*--domain | d* The domain for which the ip must be set. (including optional subdomain)

##install

1. Download or clone the repository 
2. Download the latest version of the Transip Api [https://www.transip.nl/transip/api/](https://www.transip.nl/transip/api/)
3. Copy the *Transip* directory from the zip archive into the lib directory
4. Goto [https://www.transip.nl/cp/account/api/](https://www.transip.nl/cp/account/api/) and create a Key pairs. **Make sure you disable the checkbox for whitelisted IP**
5. Copy the generated key and place it into a text file. For example: private-key.txt
6. Run the command with with the correct parameters. Voila! you're done.

```
$ git clone https://github.com/jlentink/transip-dyndns.git
$ cd transip-dyndns/lib
$ wget https://api.transip.nl/downloads/transapi_transip.nl_v5_2.tar.gz
$ tar -zxf transapi_transip.nl_v5_2.tar.gz
$ cd ..
$ ./transip-dyndns.php --username foo --private-key private-key.txt --domain foo.bar.com

```


