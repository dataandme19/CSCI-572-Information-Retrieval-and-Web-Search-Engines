import networkx as nx

G = nx.DiGraph()

f=open("PageRank.csv")
lines=f.read().splitlines()
fo = open("pageRankValues.txt", "wb")

#Add in nodes to graphs

for f in lines:
    args=f.split(',')
    G.add_node(args[0])

for f in lines:
    args=f.split(',')
    for node in args[1:]:
        if node in G:
            G.add_edge(args[0],node)

#print G.number_of_edges(),G.number_of_nodes()

#Add edges to Graph

#G.add_edge(1, 2)


G1 = nx.DiGraph(G)
pr = nx.pagerank(G1, alpha=0.9)
f1=open("URLtoFileMap.csv")
lines=f1.read().splitlines()
d={}
for f in lines:
    args=f.split(',')
    d[args[0]]=args[1]

for key in pr:
    if key in d.keys():
        fo.write("" + d[key] + "=" + ("%f" % pr[key]) + "\n")
fo.close()