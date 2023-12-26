[Chromium]: https://en.wikipedia.org/wiki/Chromium_(web_browser)
[Git command-line]: https://git-scm.com/downloads
[Google Chrome]: https://www.google.com/chrome/index.html
[Microsoft Edge (Chromium)]: https://www.microsoft.com/en-us/edge/
[Visual Studio Code]: https://code.visualstudio.com/

# Setting up Development Environment
1. Download [Git command-line] for your computer.

    - This will allow you to manage a local version of the Git repository and push commits.<br/><br/>

2. Once Git is downloaded, the repository must be cloned to your machine.

    - Navigate to the folder you would like to store your local repository using the Current Directory command.
        - Ex: "cd C:/The/Path/To/Your/Folder"
    - Once you are in the desired directory, use the clone command to clone the repository.
        - Copy this command: "git clone git@gitlab.csi.miamioh.edu:rapose/iml.git"
        - If the above does not work, copy this command: "git clone https://gitlab.csi.miamioh.edu/rapose/iml.git"
        - NOTE: After running this command, you might be prompted to enter credentials. Use your Miami Unversity email and password.<br/><br/>
    
3. Once the repository is cloned locally, you must choose a code editor.

    - It is recommended you download [Visual Studio Code].
        - Visual Studio Code is lightweight editor that allows you to download extensions for whichever language desired.
        - Initially, you should download extensions for: HTML, CSS, JavaScript and PHP.
    - Using Visual Studio Code is not a requirement though, you may use whichever code editor you prefer.<br/><br/>

4. Finally, once you have a code editor, you will need a browser to run and test your code.

    - It is recommended you download a [Chromium] browser.
        - [Microsoft Edge (Chromium)], [Google Chrome], etc.<br/><br/>

# Creating an Issue
1. Navigate to the "Boards" page in GitLab.

2. Once in "Boards", click the "+" button in the top right of the board you would like the Issue to appear (Issues can be moved).

3. A new card should appear in the corresponding board, add a title to the card.
    - Titles should capture the overall purpose of the issue. Be careful not to include unnecessary implementation details in the title.
        - Ex: Proper name, "Create Developer Guide". Improper name, "Create CONTRIBUTING.md".
        - Try to give issues a title that anyone could understand, not just developers.<br/><br/>

4. Once the card is named, a new issue will be created. Click on the card to edit the issue.

5. Once in the issue view, click the edit description button (it is located under the "New Issue" button).

    - The issue description should include a short summary of the issue and a list of tasks to complete.
    - Issues should consist of no more than 4-5 tasks.
        - Task checkboxes can be creating using the markdown, "- [ ] My Task Description".
    - If an issue has more than 5 tasks, it likely should be split into 2 or more issues.<br/><br/>

6. After completing the issue description, assign the task to yourself or the proper developer.
    - On the right-hand side of the issue view, find the "Assignees" box.
    - Either click "assign yourself" or "Edit" to assign the proper developers.<br/><br/>

7. Once all of the tasks have been completed, the issue should be completed and submitted for review. See "Submitting a Merge Request" below for more details about completing an issue.<br/><br/>

    - <b>NOTE<b>: Developers should have at least one commit for every task.
    - Commiting regularly makes it easier for reviewers to understand your changes and increases the likelihood that your merge request will be accepted.<br/><br/>

# Creating a Branch, Committing Changes and Submitting a Merge Request
Before you can create a branch you must create an issue (See "Creating an Issue" above). Once an issue is created you must, Create a Branch & Merge Request, Commit Changes and Submit the Merge Request. Each of these will be described below.

## Creating a Branch & Merge Request
1. To create a branch, navigate to the issue view for your issue (Go to "Issues" --> "Boards" and click on the desired issue).

2. Once in the issue view, click the dropdown button attached to the "Create merge request" button.

3. Ensure "Create merge request and branch" is selected (indicated by the check mark).

4. Ensure the default branch name is being used.
    - Ex: "##-the-title-of-the-branch", where ## is the number of the issue.<br/><br/>

5. Click "Create merge request" (your branch and merge request have now been created).

6. To begin editing in your branch, open your git command line (See "Setting up Development Environment" above for downloading a git command line).

7. Navigate to your local repository using the "Current Directory" command.

    - Ex: "cd C:/The/Path/To/iml"<br/><br/>

8. Once in your local git repository, pull down the most recent version using the "pull" command.

    - Copy this command: "git pull".
    - You should see the addition of your new branch amoung the changes.<br/><br/>

9. Switch your local repository to the branch you just created and pulled down. 

    - Ex: "git checkout ##-the-title-of-the-branch"<br/><br/>

10. You are now in a local version of the branch and may begin to make changes to the code.

    - To make changes, open the root "iml" folder in your code editor (See "Setting up Development Environment" for recommendations on code editors and web browsers).<br/><br/>

## Committing Changes
1. Once you have made changes to your local repository using your code editor, you must commit these changes to the Git repository.

2. To commit changes, open your git command line.

3. First you must ensure you made changes to the correct files.

    - To do this run the command: "git status"
    - This will display (in red) all of the files with unstaged changes.<br/><br/>

4. After ensuring you made changes to the correct files, you stage these changes for commit.

    - To stage a file for commit run the command: "git add relative\path\to\file.extension"
    - If all changed files are to be staged, you may stage all of them by running: "git add ."<br/><br/>

5. After staging files, you must commit the staged files and add a commit message. Commit messages are required in Git.

    - To do this run the command: 'git commit -m"My commit message"'.
    - Commit messages should detail the changes of the commit.
    - Please try and be more detailed than simply, "I have completed this task".
    - Ex: "Fixed issue when dealing with one-to-many relationships where the many is unbounded. Added styling to xml export to make file more readable."<br/><br/>

6. Once files have been committed locally, you must push the files to the remote repository using the "push" command.

    - Copy the command: "git push".
    - NOTE: After running this command, you might be prompted to enter credentials. Use your Miami Unversity email and password.<br/><br/>

7. After pushing, your changes will be added to the remote repository.

## Submitting a Merge Request
1. Once all of the tasks in an issue have been completed, the issue is ready to be submitted for merging.

    - Ensure you have pushed all of your changes for the issue by running: "git status"
    - All changes are pushed if you see, "nothing to commit, working tree clean"<br/><br/>

2. Now that all changes are in the remote repository, navigate to the IML repository on GitLab.

3. Click on the "Merge Requests" button on the left-hand side of the screen.

4. In the merge requests view, click on the merge request corresponding to your issue (it will likely say, 'WIP: Resolve "My Task Title"').

5. Once in your merge request view, click the "Resolve WIP status" button in the middle of the screen.

    - This will allow reviewers to merge this branch if approved.<br/><br/>

6. Now, you must assign the proper review to this merge request to nofity them it is ready.

    - On the right-hand side, find the "Assignees" box.
    - Click "Edit" and choose the proper reviewer.<br/><br/>

7. Once the merge request has been assigned, navigate to the "boards" page in GitLab.

    - On the left-hand side, hover over "Issues" and click "Boards" in the resulting dropdown menu.<br/><br/>

8. Find the issue card you have just submitted for merge and drag the card to the "Waiting to Merge" board.

9. Your branch has now been submitted for review and the reviewer(s) will be notified. 
