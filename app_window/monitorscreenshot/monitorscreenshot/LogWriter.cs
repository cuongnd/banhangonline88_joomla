/*
 * Created by SharpDevelop.
 * User: cuongnd
 * Date: 16/10/2016
 * Time: 4:12 CH
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Reflection;

namespace monitorscreenshot
{
	/// <summary>
	/// Description of LogWriter.
	/// </summary>
	public class LogWriter
	{
		
		
		public  LogWriter()
	    {

	    }

	    public static void LogWrite(String logMessage)
	    {
	        String m_exePath = Path.GetDirectoryName(Assembly.GetExecutingAssembly().Location);
	        try
	        {
	        	String file_log_path=m_exePath + "\\" + "log.txt";
	        	if(!File.Exists(file_log_path))
	        	{
	        		File.Create(file_log_path);
	        	}
	        	using (StreamWriter w = File.AppendText(file_log_path))
	            {
	                Log(logMessage, w);
	                
	            }
	        }
	        catch (Exception ex)
	        {
	        }
	    }
	
	    public static void Log(String logMessage, TextWriter txtWriter)
	    {
	        try
	        {
	            txtWriter.Write("\r\nLog Entry : ");
	            txtWriter.WriteLine("{0} {1}", DateTime.Now.ToLongTimeString(),
	                DateTime.Now.ToLongDateString());
	            txtWriter.WriteLine("  :");
	            txtWriter.WriteLine("  :{0}", logMessage);
	            txtWriter.WriteLine("-------------------------------");
	        }
	        catch (Exception ex)
	        {
	        }
	    }
	}
}
