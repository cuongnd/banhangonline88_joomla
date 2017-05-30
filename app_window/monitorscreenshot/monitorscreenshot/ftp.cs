/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 16/10/2016
 * Time: 1:21 CH
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Net;
using System.Windows.Forms;

namespace monitorscreenshot
{
	/// <summary>
	/// Description of ftp.
	/// </summary>
	public class ftp
	{
		private String host = null;
	    private String user = null;
	    private String pass = null;
	    private FtpWebRequest ftpRequest = null;
	    private FtpWebResponse ftpResponse = null;
	    private System.IO.Stream ftpStream = null;
	    private int bufferSize = 204800;
	        
	   /* Construct Object */
	    public ftp(String hostIP, String userName, String password) { host = hostIP; user = userName; pass = password; }
	
	    /* Download File */
	    public void download(String remoteFile, String localFile)
	    {
	    	 try
	        {
	            /* Create an FTP Request */
	            ftpRequest = (FtpWebRequest)FtpWebRequest.Create(host + "/" + remoteFile);
	            /* Log in to the FTP Server with the User Name and Password Provided */
	            ftpRequest.Credentials = new NetworkCredential(user, pass);
	            /* When in doubt, use these options */
	            ftpRequest.UseBinary = true;
	            ftpRequest.UsePassive = true;
	            ftpRequest.KeepAlive = true;
	            /* Specify the Type of FTP Request */
	            ftpRequest.Method = WebRequestMethods.Ftp.DownloadFile;
	            /* Establish Return Communication with the FTP Server */
	            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
	            /* Get the FTP Server's Response Stream */
	            ftpStream = ftpResponse.GetResponseStream();
	            /* Open a File Stream to Write the Downloaded File */
	            FileStream localFileStream = new FileStream(localFile, FileMode.Create);
	            /* Buffer for the Downloaded Data */
	            Byte[] byteBuffer = new Byte[bufferSize];
	            int bytesRead = ftpStream.Read(byteBuffer, 0, bufferSize);
	            /* Download the File by Writing the Buffered Data Until the Transfer is Complete */
	            try
	            {
	                while (bytesRead > 0)
	                {
	                    localFileStream.Write(byteBuffer, 0, bytesRead);
	                    bytesRead = ftpStream.Read(byteBuffer, 0, bufferSize);
	                }
	            }
	            catch (Exception ex) { Console.WriteLine(ex.ToString()); }
	            /* Resource Cleanup */
	            localFileStream.Close();
	            ftpStream.Close();
	            ftpResponse.Close();
	            ftpRequest = null;
	        }
	        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
	        return;
		  }
	    /* Upload File */
    public void upload(String remoteFile, String localFile)
    {
    
        try
        {
            /* Create an FTP Request */
            FtpWebRequest ftpClient = (FtpWebRequest)FtpWebRequest.Create(host + remoteFile);
				ftpClient.Credentials = new System.Net.NetworkCredential(user, pass);
				ftpClient.Method = System.Net.WebRequestMethods.Ftp.UploadFile;
				ftpClient.UseBinary = true;
				ftpClient.UsePassive = true;
				ftpClient.KeepAlive = true;
				System.IO.FileInfo fi = new System.IO.FileInfo(localFile);
				ftpClient.ContentLength = fi.Length;
				Byte[] buffer = new Byte[bufferSize];
				int bytes = 0;
				int total_bytes = (int)fi.Length;

				System.IO.FileStream fs = fi.OpenRead();
				System.IO.Stream rs = ftpClient.GetRequestStream();
				while (total_bytes > 0)
				{
				   bytes = fs.Read(buffer, 0, buffer.Length);
				   rs.Write(buffer, 0, bytes);
				   total_bytes = total_bytes - bytes;
				}
				//fs.Flush();
				rs.Close();
				FtpWebResponse uploadResponse = (FtpWebResponse)ftpClient.GetResponse();

				uploadResponse.Close();
        }
        catch (Exception ex) {
        	LogWriter.LogWrite(ex.ToString());
        	//Console.WriteLine(ex.ToString()); 
        }
        return;
    }
	    
	
    /* Delete File */
    public void delete(String deleteFile)
    {
        try
        {
            /* Create an FTP Request */
            ftpRequest = (FtpWebRequest)WebRequest.Create(host + "/" + deleteFile);
            /* Log in to the FTP Server with the User Name and Password Provided */
            ftpRequest.Credentials = new NetworkCredential(user, pass);
            /* When in doubt, use these options */
            ftpRequest.UseBinary = true;
            ftpRequest.UsePassive = true;
            ftpRequest.KeepAlive = true;
            /* Specify the Type of FTP Request */
            ftpRequest.Method = WebRequestMethods.Ftp.DeleteFile;
            /* Establish Return Communication with the FTP Server */
            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
            /* Resource Cleanup */
            ftpResponse.Close();
            ftpRequest = null;
        }
        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        return;
    }

    /* Rename File */
    public void rename(String currentFileNameAndPath, String newFileName)
    {
        try
        {
            /* Create an FTP Request */
            ftpRequest = (FtpWebRequest)WebRequest.Create(host + "/" + currentFileNameAndPath);
            /* Log in to the FTP Server with the User Name and Password Provided */
            ftpRequest.Credentials = new NetworkCredential(user, pass);
            /* When in doubt, use these options */
            ftpRequest.UseBinary = true;
            ftpRequest.UsePassive = true;
            ftpRequest.KeepAlive = true;
            /* Specify the Type of FTP Request */
            ftpRequest.Method = WebRequestMethods.Ftp.Rename;
            /* Rename the File */
            ftpRequest.RenameTo = newFileName;
            /* Establish Return Communication with the FTP Server */
            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
            /* Resource Cleanup */
            ftpResponse.Close();
            ftpRequest = null;
        }
        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        return;
    }

    /* Create a New Directory on the FTP Server */
    public void createDirectory(String newDirectory)
    {
        try
        {
            /* Create an FTP Request */
            ftpRequest = (FtpWebRequest)WebRequest.Create(host + "/" + newDirectory);
            /* Log in to the FTP Server with the User Name and Password Provided */
            ftpRequest.Credentials = new NetworkCredential(user, pass);
            /* When in doubt, use these options */
            ftpRequest.UseBinary = true;
            ftpRequest.UsePassive = true;
            ftpRequest.KeepAlive = true;
            /* Specify the Type of FTP Request */
            ftpRequest.Method = WebRequestMethods.Ftp.MakeDirectory;
            /* Establish Return Communication with the FTP Server */
            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
            /* Resource Cleanup */
            ftpResponse.Close();
            ftpRequest = null;
        }
        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        return;
    }

    /* Get the Date/Time a File was Created */
    public String getFileCreatedDateTime(String fileName)
    {
        try
        {
            /* Create an FTP Request */
            ftpRequest = (FtpWebRequest)FtpWebRequest.Create(host + "/" + fileName);
            /* Log in to the FTP Server with the User Name and Password Provided */
            ftpRequest.Credentials = new NetworkCredential(user, pass);
            /* When in doubt, use these options */
            ftpRequest.UseBinary = true;
            ftpRequest.UsePassive = true;
            ftpRequest.KeepAlive = true;
            /* Specify the Type of FTP Request */
            ftpRequest.Method = WebRequestMethods.Ftp.GetDateTimestamp;
            /* Establish Return Communication with the FTP Server */
            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
            /* Establish Return Communication with the FTP Server */
            ftpStream = ftpResponse.GetResponseStream();
            /* Get the FTP Server's Response Stream */
            StreamReader ftpReader = new StreamReader(ftpStream);
            /* Store the Raw Response */
            String fileInfo = null;
            /* Read the Full Response Stream */
            try { fileInfo = ftpReader.ReadToEnd(); }
            catch (Exception ex) { Console.WriteLine(ex.ToString()); }
            /* Resource Cleanup */
            ftpReader.Close();
            ftpStream.Close();
            ftpResponse.Close();
            ftpRequest = null;
            /* Return File Created Date Time */
            return fileInfo;
        }
        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        /* Return an Empty String Array if an Exception Occurs */
        return "";
    }

    /* Get the Size of a File */
    public String getFileSize(String fileName)
    {
        try
        {
            /* Create an FTP Request */
            ftpRequest = (FtpWebRequest)FtpWebRequest.Create(host + "/" + fileName);
            /* Log in to the FTP Server with the User Name and Password Provided */
            ftpRequest.Credentials = new NetworkCredential(user, pass);
            /* When in doubt, use these options */
            ftpRequest.UseBinary = true;
            ftpRequest.UsePassive = true;
            ftpRequest.KeepAlive = true;
            /* Specify the Type of FTP Request */
            ftpRequest.Method = WebRequestMethods.Ftp.GetFileSize;
            /* Establish Return Communication with the FTP Server */
            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
            /* Establish Return Communication with the FTP Server */
            ftpStream = ftpResponse.GetResponseStream();
            /* Get the FTP Server's Response Stream */
            StreamReader ftpReader = new StreamReader(ftpStream);
            /* Store the Raw Response */
            String fileInfo = null;
            /* Read the Full Response Stream */
            try { while (ftpReader.Peek() != -1) { fileInfo = ftpReader.ReadToEnd(); } }
            catch (Exception ex) { Console.WriteLine(ex.ToString()); }
            /* Resource Cleanup */
            ftpReader.Close();
            ftpStream.Close();
            ftpResponse.Close();
            ftpRequest = null;
            /* Return File Size */
            return fileInfo;
        }
        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        /* Return an Empty String Array if an Exception Occurs */
        return "";
    }

    /* List Directory Contents File/Folder Name Only */
    public String[] directoryListSimple(String directory)
    {
        try
        {
            /* Create an FTP Request */
            ftpRequest = (FtpWebRequest)FtpWebRequest.Create(host + "/" + directory);
            /* Log in to the FTP Server with the User Name and Password Provided */
            ftpRequest.Credentials = new NetworkCredential(user, pass);
            /* When in doubt, use these options */
            ftpRequest.UseBinary = true;
            ftpRequest.UsePassive = true;
            ftpRequest.KeepAlive = true;
            /* Specify the Type of FTP Request */
            ftpRequest.Method = WebRequestMethods.Ftp.ListDirectory;
            /* Establish Return Communication with the FTP Server */
            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
            /* Establish Return Communication with the FTP Server */
            ftpStream = ftpResponse.GetResponseStream();
            /* Get the FTP Server's Response Stream */
            StreamReader ftpReader = new StreamReader(ftpStream);
            /* Store the Raw Response */
            String directoryRaw = null;
            /* Read Each Line of the Response and Append a Pipe to Each Line for Easy Parsing */
            try { while (ftpReader.Peek() != -1) { directoryRaw += ftpReader.ReadLine() + "|"; } }
            catch (Exception ex) { Console.WriteLine(ex.ToString()); }
            /* Resource Cleanup */
            ftpReader.Close();
            ftpStream.Close();
            ftpResponse.Close();
            ftpRequest = null;
            /* Return the Directory Listing as a String Array by Parsing 'directoryRaw' with the Delimiter you Append (I use | in This Example) */
            try { String[] directoryList = directoryRaw.Split("|".ToCharArray()); return directoryList; }
            catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        }
        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        /* Return an Empty String Array if an Exception Occurs */
        return new String[] { "" };
    }

    /* List Directory Contents in Detail (Name, Size, Created, etc.) */
    public String[] directoryListDetailed(String directory)
    {
        try
        {
            /* Create an FTP Request */
            ftpRequest = (FtpWebRequest)FtpWebRequest.Create(host + "/" + directory);
            /* Log in to the FTP Server with the User Name and Password Provided */
            ftpRequest.Credentials = new NetworkCredential(user, pass);
            /* When in doubt, use these options */
            ftpRequest.UseBinary = true;
            ftpRequest.UsePassive = true;
            ftpRequest.KeepAlive = true;
            /* Specify the Type of FTP Request */
            ftpRequest.Method = WebRequestMethods.Ftp.ListDirectoryDetails;
            /* Establish Return Communication with the FTP Server */
            ftpResponse = (FtpWebResponse)ftpRequest.GetResponse();
            /* Establish Return Communication with the FTP Server */
            ftpStream = ftpResponse.GetResponseStream();
            /* Get the FTP Server's Response Stream */
            StreamReader ftpReader = new StreamReader(ftpStream);
            /* Store the Raw Response */
            String directoryRaw = null;
            /* Read Each Line of the Response and Append a Pipe to Each Line for Easy Parsing */
            try { while (ftpReader.Peek() != -1) { directoryRaw += ftpReader.ReadLine() + "|"; } }
            catch (Exception ex) { Console.WriteLine(ex.ToString()); }
            /* Resource Cleanup */
            ftpReader.Close();
            ftpStream.Close();
            ftpResponse.Close();
            ftpRequest = null;
            /* Return the Directory Listing as a String Array by Parsing 'directoryRaw' with the Delimiter you Append (I use | in This Example) */
            try { String[] directoryList = directoryRaw.Split("|".ToCharArray()); return directoryList; }
            catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        }
        catch (Exception ex) { Console.WriteLine(ex.ToString()); }
        /* Return an Empty String Array if an Exception Occurs */
        return new String[] { "" };
    }
    
	}
}

