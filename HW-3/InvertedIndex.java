import java.io.IOException;
import java.util.*;
import java.util.StringTokenizer;
import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.Writable;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.MapWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;

public class InvertedIndex {

  public static class TokenizerMapper extends Mapper<Object, Text, Text, Text>{

    private static final IntWritable one = new IntWritable(1);
    private Text word = new Text();
    private Text docID = new Text();
    private Text docContent = new Text();
    public void map(Object key, Text value, Context context) throws IOException, InterruptedException {
      String[] tokens = value.toString().split("\\t");
      docID.set(tokens[0]);
      docContent.set(tokens[1]);
      StringTokenizer itr = new StringTokenizer(docContent.toString());
      while (itr.hasMoreTokens()) {
        word.set(itr.nextToken());
        context.write(word,docID);
      }   
    }    
  }     

public static class IntSumReducer extends Reducer<Text, Text, Text, Text>{
    private Text docId = new Text();
    private static final IntWritable one = new IntWritable(1);
    
      public void reduce(Text key, Iterable<Text> values,Context context) throws IOException, InterruptedException {
      int sum = 0;
      HashMap<String,Integer> hmap = new HashMap<String,Integer>();
      for(Text val : values) {
        sum = 0;
        if(hmap.containsKey(val.toString())){
		
                hmap.put(val.toString(),hmap.get(val.toString()) + 1);
        }
        else{
                hmap.put(val.toString(),1);
        }
      }
        context.write(key,new Text(hmap.toString()));
    }
  }
public static void main(String[] args) throws Exception {
    Configuration conf = new Configuration();
    conf.set("mapreduce.input.keyvaluelinerecordreader.key.value.separator", ":");
    Job job = Job.getInstance(conf, "word count");
    job.setJarByClass(InvertedIndex.class);
    job.setMapperClass(TokenizerMapper.class);
    job.setReducerClass(IntSumReducer.class);
    job.setMapOutputKeyClass(Text.class);
    job.setMapOutputValueClass(Text.class);
    job.setOutputKeyClass(Text.class);
    job.setOutputValueClass(Text.class);
    FileInputFormat.addInputPath(job, new Path(args[0]));
    FileOutputFormat.setOutputPath(job, new Path(args[1]));
    System.exit(job.waitForCompletion(true) ? 0 : 1);
  }
}