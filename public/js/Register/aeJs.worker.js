// Import library
importScripts("aeJs.js");

self.onmessage = function(content)
{
    switch(content.data.action)
    {
        case "encrypt":
            self.postMessage({
                action: "loadFile",
                progress: "begin"
            });
            
            var reader = new FileReaderSync();
            var plaintext = reader.readAsText(content.data.file);
            
            self.postMessage({
                action: "loadFile",
                progress: "end"
            });
            
            var ciphertext = aes.actions.encrypt(plaintext, content.data.password, content.data.bits);
            
            self.postMessage({
                action: "encryptFile",
                progress: "end",
                ciphertext: ciphertext
            });
            break;
            
        case "decrypt":
            break;
    }
}
