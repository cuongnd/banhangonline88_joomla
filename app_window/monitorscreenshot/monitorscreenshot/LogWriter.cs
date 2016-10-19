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
		private volatile static LogWriter uniqueInstance;
		
		
		
		private String m_exePath = String.Empty;
		
		
		public static LogWriter getInstance()
		{
			if (uniqueInstance == null)
	        {
				uniqueInstance = new LogWriter("log");
	        }
	        return uniqueInstance;
        
		}
		
	    public  LogWriter(String logMessage)
	    {
	        LogWrite(logMessage);
	    }
	    public void LogWrite(String logMessage)
	    {
	        m_exePath = Path.GetDirectoryName(Assembly.GetExecutingAssembly().Location);
	        try
	        {
	            using (StreamWriter w = File.AppendText(m_exePath + "\\" + "log.txt"))
	            {
	                Log(logMessage, w);
	            }
	        }
	        catch (Exception ex)
	        {
	        }
	    }
	
	    public void Log(String logMessage, TextWriter txtWriter)
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
