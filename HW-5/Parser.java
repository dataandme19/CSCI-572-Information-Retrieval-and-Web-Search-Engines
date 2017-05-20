import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.tika.metadata.Metadata;
import org.apache.tika.parser.AutoDetectParser;
import org.apache.tika.parser.ParseContext;
import org.apache.tika.parser.Parser;
import org.apache.tika.sax.BodyContentHandler;

public class Main {
	private static ArrayList<Double> ttr = new ArrayList<Double>();
	static String output = "E:\\USC\\ACADEMICS\\SPRING 2017\\IR\\Assignment\\HW 5\\big.txt";
	static PrintWriter extract=null,store=null;
	public static void main(String[] args) throws FileNotFoundException {
		 final File fileOrFolder = new File("E:\\USC\\ACADEMICS\\SPRING 2017\\IR\\Assignment\\HW 5\\Crawled websites data\\LATimesDownloadData");
		 
		File out_file = new File(output);
		if(out_file.exists())
			out_file.delete();
		 int count =0;
		 
		 
	 		String title=null;
		 for (final File fileEntry : fileOrFolder.listFiles()){
			// if(count<100){
		 FileInputStream inputstream=new FileInputStream(fileEntry);
 	    ParseContext context = new ParseContext();
 	    Parser parser = new AutoDetectParser();
 	   BodyContentHandler handler = new BodyContentHandler();
 	    Metadata metadata = new Metadata();
 	   try{
   	    parser.parse(inputstream, handler, metadata, context);
   	 title =  metadata.get("title");
   	 storeObject(handler.toString());
   	 tagRatio();
   	 content();
   	    count++;
   	    }catch(Exception e){
   	    	continue;
   	    }
 	 
 		System.out.println(title+" "+count);
			 
	}
	}
	public static  void tagRatio() throws FileNotFoundException, IOException,NullPointerException{
		  BufferedReader br= new BufferedReader(new FileReader("temp"));
		   long x,y;
		   String line=null,stripped=null;
		   while(null!=(line=br.readLine())){
			   x=0;y=0;
			   try{
			   Pattern p = Pattern.compile("<(\"[^\"]*\"|'[^']*'|[^'\">])*>");
		       Matcher m = p.matcher(line);
		       while(m.find())
		    	   y++;
			   stripped = line.replaceAll("<[^>]*>", "");
			   if(!(stripped.trim().isEmpty())){
				   stripped = stripped.replaceAll("\t", "");
				   stripped = stripped.trim();
			   x=stripped.length();  
			   //System.out.println(stripped);
			   }
			   if(y==0)
				   y=1;
			   ttr.add((double)x/y);
			   }
			   catch(StackOverflowError e){
				   continue;
			   }
			   catch(Exception e){
				   break;
			   }
		   }
		   br.close();
	}
	public static void content() throws IOException{
		BufferedReader br= new BufferedReader(new FileReader("temp"));
		for(int i=0;i<ttr.size();i++){
			extract = new PrintWriter(new BufferedWriter(new FileWriter(output,true)));
			   String line = null;
			   if(null!=(line = br.readLine())){
			   if(ttr.get(i)>0){
				   line=line.trim();
				   line.replaceAll("\n", " ");
				  extract.println(line);
					   }
				   }
			   extract.close();
			   }
		
		   
		   br.close();
	}
	public static void storeObject(String temp){
		try {
			store = new PrintWriter(new BufferedWriter(new FileWriter("temp")));
			
			temp = temp.replaceAll("\t", " ");
			temp = temp.trim();
			store.write(temp);
			//System.out.println(temp);
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} 
		catch(Exception ie){}
		finally{
			try{
				store.close();
			} catch (Exception ex){
				
			}
		}
	}
		

}