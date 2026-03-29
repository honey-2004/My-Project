from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Dict, Any

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class PipelineRequest(BaseModel):
    nodes: List[Dict[str, Any]]
    edges: List[Dict[str, Any]]

@app.get('/')
def read_root():
    return {'Ping': 'Pong'}

@app.post('/pipelines/parse')
def parse_pipeline(pipeline: PipelineRequest):
    nodes = pipeline.nodes
    edges = pipeline.edges
    
    num_nodes = len(nodes)
    num_edges = len(edges)
    
    is_dag = check_dag(nodes, edges)
    
    return {
        'num_nodes': num_nodes,
        'num_edges': num_edges,
        'is_dag': is_dag
    }

def check_dag(nodes: List[Dict[str, Any]], edges: List[Dict[str, Any]]) -> bool:
    if not nodes:
        return True
    
    node_ids = {node['id'] for node in nodes}
    in_degree = {node_id: 0 for node_id in node_ids}
    adjacency_list = {node_id: [] for node_id in node_ids}
    
    for edge in edges:
        source = edge.get('source')
        target = edge.get('target')
        
        if source in node_ids and target in node_ids:
            adjacency_list[source].append(target)
            in_degree[target] += 1
    
    queue = [node_id for node_id in node_ids if in_degree[node_id] == 0]
    processed_count = 0
    
    while queue:
        current = queue.pop(0)
        processed_count += 1
        
        # Reduce in-degree for all neighbors
        for neighbor in adjacency_list[current]:
            in_degree[neighbor] -= 1
            if in_degree[neighbor] == 0:
                queue.append(neighbor)
    
    return processed_count == len(node_ids)
