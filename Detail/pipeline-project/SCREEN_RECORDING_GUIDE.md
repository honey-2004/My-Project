# Screen Recording Guide - Frontend Demonstration

## Overview
This guide helps you create a comprehensive screen recording that demonstrates all the features of your pipeline project, even if you can only show the frontend.

---

## Pre-Recording Checklist

### Before You Start Recording:

1. **Start the Backend** (in background):
   - Open terminal
   - Navigate to `/backend`
   - Run: `uvicorn main:app --reload`
   - Keep it running (don't close the terminal)

2. **Start the Frontend**:
   - Open a new terminal
   - Navigate to `/frontend`
   - Run: `npm start`
   - Wait for browser to open at `http://localhost:3000`

3. **Prepare Your Screen**:
   - Close unnecessary applications
   - Make browser window full screen or large enough
   - Clear browser cache if needed
   - Have a clean workspace visible

---

## Screen Recording Script (Step-by-Step)

### Part 1: Show the Interface (30 seconds)

**What to Show:**
1. **Toolbar**: Point out the draggable buttons (Input, LLM, Output, Text)
   - Show the gradient button styling
   - Mention they match the Submit button design

2. **Canvas Area**: Show the empty workspace
   - Point out the grid background
   - Show the dark theme and professional styling

3. **Overall Design**: 
   - Highlight the modern, dark-themed interface
   - Show the unified color scheme

**What to Say:**
- "Here's the main interface with a modern dark theme"
- "The toolbar contains draggable node buttons with gradient styling"
- "The canvas is where we'll build our pipeline"

---

### Part 2: Demonstrate Node Abstraction (2 minutes)

**What to Show:**
1. **Drag and Drop Nodes**:
   - Drag an Input node onto the canvas
   - Drag an LLM node onto the canvas
   - Drag an Output node onto the canvas
   - Drag a Text node onto the canvas
   - Drag a Filter node onto the canvas
   - Drag a Transform node onto the canvas
   - Drag a Merge node onto the canvas
   - Drag an API node onto the canvas
   - Drag a Note node onto the canvas

2. **Show Node Consistency**:
   - Point out that all nodes have the same base structure
   - Show the consistent styling (rounded corners, shadows, hover effects)
   - Mention that they all use the BaseNode component

3. **Show Node Variety**:
   - Click on different nodes to show their unique configurations
   - Show Input node with Name and Type fields
   - Show LLM node with its connection points
   - Show Text node with its text area
   - Show Filter node with condition dropdown
   - Show Transform node with transform type selector
   - Show Merge node with operator selection
   - Show API node with method and endpoint fields
   - Show Note node with its note textarea

**What to Say:**
- "I can drag different node types from the toolbar"
- "All nodes share a common base structure through the BaseNode abstraction"
- "Each node has its own unique functionality while maintaining consistent styling"
- "This abstraction makes it easy to create new node types"

---

### Part 3: Demonstrate Styling (1 minute)

**What to Show:**
1. **Hover Effects**:
   - Hover over nodes to show the hover effects
   - Show the border color change
   - Show the shadow enhancement
   - Show the slight lift effect

2. **Button Styling**:
   - Hover over toolbar buttons
   - Show the gradient background
   - Show the hover animation
   - Point out the Submit button matches the same style

3. **Overall Design**:
   - Pan around the interface
   - Show the color scheme consistency
   - Show the professional appearance

**What to Say:**
- "The interface has a modern, professional design with a dark theme"
- "All elements have smooth hover effects and transitions"
- "The styling is consistent throughout the application"
- "Buttons use a gradient design that matches across the interface"

---

### Part 4: Demonstrate Text Node Features (2 minutes)

**What to Show:**

1. **Auto-Resizing Feature**:
   - Click on a Text node
   - Start typing in the text area
   - Type multiple lines of text
   - Show how the text area automatically grows taller
   - Delete some text and show it shrinks back
   - Say: "The text area automatically resizes based on content"

2. **Dynamic Variable Handles**:
   - Clear the text area
   - Type: `Hello {{ name }}, welcome to {{ company }}`
   - Show how handles appear on the left side
   - Point out the variable names ("name" and "company")
   - Type another variable: `Your email is {{ email }}`
   - Show the new handle appears
   - Remove a variable and show the handle disappears
   - Say: "When I type variables in double curly brackets, connection points are automatically created"

3. **Variable Labeling**:
   - Show the variable names displayed next to the handles
   - Show how they're clearly labeled
   - Say: "Each variable gets its own labeled connection point"

**What to Say:**
- "The Text node has two key features"
- "First, it auto-resizes as you type more text"
- "Second, it automatically creates connection points for variables defined with double curly brackets"
- "This allows users to connect other nodes to provide values for these variables"

---

### Part 5: Build a Complete Pipeline (2 minutes)

**What to Show:**
1. **Create a Simple Pipeline**:
   - Drag an Input node
   - Drag a Text node
   - Drag an Output node
   - Connect Input → Text → Output
   - Show the connections with animated edges

2. **Create a More Complex Pipeline**:
   - Add more nodes (Filter, Transform, etc.)
   - Connect them in a chain
   - Show multiple connections
   - Show how nodes can have multiple inputs/outputs

3. **Show Pipeline Structure**:
   - Pan around to show the complete pipeline
   - Show all connections
   - Say: "This is a complete pipeline with multiple nodes connected"

**What to Say:**
- "I can build pipelines by connecting nodes together"
- "The connections show data flow through the pipeline"
- "Nodes can have multiple inputs and outputs"

---

### Part 6: Demonstrate Backend Integration (2 minutes)

**What to Show:**
1. **Show the Submit Button**:
   - Scroll down or point to the Submit button
   - Show it has the same gradient styling as toolbar buttons
   - Say: "The Submit button sends the pipeline to the backend for analysis"

2. **Click Submit**:
   - Click the Submit button
   - Wait for the response (should be quick)
   - Show the alert popup appears

3. **Show the Results**:
   - Read the alert content out loud:
     - "Pipeline Analysis Results:"
     - "Number of Nodes: [count]"
     - "Number of Edges: [count]"
     - "Is DAG: Yes/No"
   - Point out each value
   - Say: "The backend analyzed the pipeline and returned these results"

4. **Test with Different Pipelines**:
   - Create a simple pipeline (2-3 nodes) and submit
   - Show the results
   - Create a more complex pipeline and submit
   - Show the results
   - Create a pipeline with a cycle (if possible) and show "Is DAG: No"

**What to Say:**
- "When I click Submit, the frontend sends the pipeline data to the backend"
- "The backend calculates the number of nodes and edges"
- "It also checks if the pipeline is a valid DAG (Directed Acyclic Graph)"
- "The results are displayed in a user-friendly alert"

---

### Part 7: Show Error Handling (30 seconds)

**What to Show:**
1. **Stop the Backend** (if you want to show error handling):
   - Go to the backend terminal
   - Stop the server (Ctrl+C)
   - Go back to frontend
   - Try to submit
   - Show the error message appears
   - Say: "The application handles errors gracefully"

**OR Skip this if backend is working fine**

---

## Complete Recording Timeline

| Time | Section | Duration |
|------|---------|----------|
| 0:00 - 0:30 | Interface Overview | 30 sec |
| 0:30 - 2:30 | Node Abstraction Demo | 2 min |
| 2:30 - 3:30 | Styling Demo | 1 min |
| 3:30 - 5:30 | Text Node Features | 2 min |
| 5:30 - 7:30 | Build Pipeline | 2 min |
| 7:30 - 9:30 | Backend Integration | 2 min |
| 9:30 - 10:00 | Wrap Up | 30 sec |

**Total Time: ~10 minutes**

---

## Key Points to Emphasize

### 1. Node Abstraction
- ✅ All nodes use BaseNode component
- ✅ Consistent structure and styling
- ✅ Easy to create new node types
- ✅ Show at least 5 different node types

### 2. Styling
- ✅ Modern, professional design
- ✅ Dark theme with gradient accents
- ✅ Smooth animations and hover effects
- ✅ Unified design language

### 3. Text Node Features
- ✅ Auto-resizing text area
- ✅ Dynamic variable handles from `{{ variable }}` syntax
- ✅ Real-time handle creation/removal

### 4. Backend Integration
- ✅ Submit button sends pipeline data
- ✅ Backend analyzes and returns results
- ✅ Results displayed in alert
- ✅ Shows node count, edge count, and DAG validation

---

## Tips for a Great Recording

### Do's:
- ✅ Speak clearly and explain what you're doing
- ✅ Show the mouse cursor movements
- ✅ Pause briefly after each action
- ✅ Highlight key features as you demonstrate them
- ✅ Show both simple and complex examples
- ✅ Make sure the backend is running before recording
- ✅ Test everything once before recording

### Don'ts:
- ❌ Don't rush through features
- ❌ Don't skip important demonstrations
- ❌ Don't forget to show the Submit button working
- ❌ Don't show errors unless demonstrating error handling
- ❌ Don't make the recording too long (keep it under 10 minutes)

---

## Quick Demo Script (Short Version - 5 minutes)

If you need a shorter version:

1. **Show Interface** (30 sec) - Toolbar, canvas, styling
2. **Drag Multiple Nodes** (1 min) - Show 5+ different node types
3. **Text Node Features** (1.5 min) - Auto-resize and variables
4. **Build Pipeline** (1 min) - Connect nodes together
5. **Submit & Results** (1 min) - Click submit, show alert with results

---

## What They're Looking For

The assessors want to see:
1. ✅ **Working application** - Everything functions properly
2. ✅ **All features demonstrated** - Node abstraction, styling, text features, backend integration
3. ✅ **Professional presentation** - Clean, clear demonstration
4. ✅ **Understanding** - You can explain what you built

---

## Final Checklist Before Recording

- [ ] Backend is running (`uvicorn main:app --reload`)
- [ ] Frontend is running (`npm start`)
- [ ] Browser is open and application loads
- [ ] All nodes can be dragged and dropped
- [ ] Text node features work (resize and variables)
- [ ] Submit button works and shows results
- [ ] Screen recording software is ready
- [ ] Microphone is working (if doing voiceover)
- [ ] You've practiced the demo once

---

## Sample Script (What to Say)

**Opening:**
"Hi, I'm going to demonstrate the pipeline builder application I built. This includes node abstraction, styling, text node features, and backend integration."

**During Demo:**
- "As you can see, I can drag different node types from the toolbar..."
- "All nodes share a common base structure through the BaseNode abstraction..."
- "The Text node has two key features: auto-resizing and dynamic variable handles..."
- "When I click Submit, the backend analyzes the pipeline and returns the results..."

**Closing:**
"This completes the demonstration. The application successfully implements all required features including node abstraction, modern styling, advanced text node functionality, and full backend integration."

---

## Remember

- **You can only show the frontend** - That's fine! The backend integration is demonstrated through the Submit button and alert results
- **The backend must be running** - Even though you don't show it, it needs to be running for Submit to work
- **Show all features** - Make sure to demonstrate everything mentioned in the requirements
- **Be confident** - You built this, show it off!

Good luck with your screen recording! 🎥

