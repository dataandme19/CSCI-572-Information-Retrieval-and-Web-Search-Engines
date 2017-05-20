package edu.uci.ics.crawler4j.examples.basic;

import java.util.ArrayList;



public class CrawlState {
    ArrayList<UrlInfo> attemptUrls;
    ArrayList<UrlInfo> visitedUrls;
    ArrayList<UrlInfo> discoveredUrls;

    public CrawlState() {
        attemptUrls = new ArrayList<UrlInfo>();
        visitedUrls = new ArrayList<UrlInfo>();
        discoveredUrls = new ArrayList<UrlInfo>();
    }
}